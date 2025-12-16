<?php

use Lib\Billing\Events\CheckoutIntent;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

use function Pest\Laravel\get;

uses(TestCase::class, LazilyRefreshDatabase::class);

beforeEach(fn () => Event::fake(CheckoutIntent::class));

test('show products page to guest', function () {
    get(route('billing.products'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products')
            ->where('has_subscription', false)
            ->where('has_incomplete_payment', false)
        );

    Event::assertNotDispatched(CheckoutIntent::class);
});

test('show products page to authenticated user', function () {
    actors()->owner(actingAs: true);

    get(route('billing.products'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products')
            ->where('has_subscription', false)
            ->where('has_incomplete_payment', false)
        );
});

test('dispatch checkout intent', function () {
    actors()->owner(actingAs: true);

    get(route('billing.products'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products')
        );

    Event::assertDispatched(CheckoutIntent::class);
});
