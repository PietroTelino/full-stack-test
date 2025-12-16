<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.bank.url', 'http://localhost/dev-bank');
    }

    /**
     * Generate a bank billet for the given invoice
     *
     * @param int $invoiceId
     * @param float $amount
     * @param \Carbon\Carbon $dueDate
     * @return array
     * @throws \Exception
     */
    public function generateBillet(int $invoiceId, Customer $customer, float $amount, $dueDate): array
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->baseUrl}/api/billets", [
                    'name' => $customer->name,
                    'document' => $customer->document,
                    'amount' => $amount,
                    'due_date' => $dueDate->format('Y-m-d'),
                ]);

            if (!$response->successful()) {
                Log::error('Bank billet generation failed', [
                    'invoice_id' => $invoiceId,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                throw new \Exception('Failed to generate bank billet: ' . $response->body());
            }

            $data = $response->json();

            return [
                'code' => $data['code'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'bank_response' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('Bank service error', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Check billet status
     *
     * @param string $code
     * @return array|null
     */
    public function getBillet(string $code): ?array
    {
        try {
            $response = Http::timeout(30)
                ->get("{$this->baseUrl}/api/billets/{$code}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Bank billet status check failed', [
                'code' => $code,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
