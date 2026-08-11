<?php

use App\Models\Customer;
use App\Services\BankService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

it('successfully generates a billet and returns parsed data', function () {
    $customer = new Customer([
        'name' => 'John Doe',
        'document' => '12345678901',
    ]);

    config(['services.bank.url' => 'http://fake-bank.test']);

    Http::fake([
        'http://fake-bank.test/api/billets*' => Http::response([
            'code' => 'BLT20250101ABC12345',
            'expires_at' => '2025-01-31T23:59:59Z',
            'status' => 'pending',
        ], 201),
    ]);

    Http::preventStrayRequests();

    $service = new BankService();

    $result = $service->generateBillet(
        invoiceId: 1,
        customer: $customer,
        amount: 10000.0,
        dueDate: Carbon::parse('2025-01-31'),
    );

    Http::assertSent(function ($request) use ($customer) {
        return $request->url() === 'http://fake-bank.test/api/billets'
            && $request['name'] === $customer->name
            && $request['document'] === $customer->document
            && ((float) $request['amount']) === 10000.0
            && $request['due_date'] === '2025-01-31';
    });

    expect($result)->toMatchArray([
        'code' => 'BLT20250101ABC12345',
        'expires_at' => '2025-01-31T23:59:59Z',
    ])->and($result['bank_response']['status'])->toBe('pending');
});

it('throws when bank API responds with error and logs the failure', function () {
    $customer = new Customer([
        'name' => 'John Doe',
        'document' => '12345678901',
    ]);

    config(['services.bank.url' => 'http://fake-bank.test']);

    Http::fake([
        'http://fake-bank.test/api/billets*' => Http::response('something went wrong', 500),
    ]);

    Http::preventStrayRequests();

    Log::spy();

    $service = new BankService();

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Failed to generate bank billet: something went wrong');

    try {
        $service->generateBillet(
            invoiceId: 99,
            customer: $customer,
            amount: 5000.0,
            dueDate: Carbon::parse('2025-01-31'),
        );
    } finally {
        Log::shouldHaveReceived('error')->withArgs(function (string $message, array $context) {
            return $message === 'Bank billet generation failed'
                && ($context['invoice_id'] ?? null) === 99
                && ($context['status'] ?? null) === 500
                && ($context['response'] ?? null) === 'something went wrong';
        });

        Log::shouldHaveReceived('error')->withArgs(function (string $message, array $context) {
            return $message === 'Bank service error'
                && ($context['invoice_id'] ?? null) === 99
                && isset($context['error'])
                && str_contains($context['error'], 'Failed to generate bank billet');
        });
    }
});

it('returns billet data when getBillet succeeds', function () {
    config(['services.bank.url' => 'http://fake-bank.test']);

    Http::fake([
        'http://fake-bank.test/api/billets/BLT123' => Http::response([
            'code' => 'BLT123',
            'status' => 'pending',
        ], 200),
    ]);

    Http::preventStrayRequests();

    $service = new BankService();

    $result = $service->getBillet('BLT123');

    expect($result)->toMatchArray([
        'code' => 'BLT123',
        'status' => 'pending',
    ]);
});

it('returns null when getBillet receives non-success status', function () {
    config(['services.bank.url' => 'http://fake-bank.test']);

    Http::fake([
        'http://fake-bank.test/api/billets/BLT404' => Http::response('not found', 404),
    ]);

    Http::preventStrayRequests();

    $service = new BankService();

    $result = $service->getBillet('BLT404');

    expect($result)->toBeNull();
});

it('returns null and logs error when exception is thrown in getBillet', function () {
    config(['services.bank.url' => 'http://fake-bank.test']);

    Log::spy();

    Http::fake([
        'http://fake-bank.test/api/billets/BLTERR' => function () {
            throw new Exception('network error');
        },
    ]);

    Http::preventStrayRequests();

    $service = new BankService();

    $result = $service->getBillet('BLTERR');

    expect($result)->toBeNull();

    Log::shouldHaveReceived('error')->withArgs(function (string $message, array $context) {
        return $message === 'Bank billet status check failed'
            && ($context['code'] ?? null) === 'BLTERR'
            && ($context['error'] ?? null) === 'network error';
    });
});
