<?php

namespace App\Jobs;

use App\Models\BankBillet;
use App\Services\BankService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchBankBilletBarcode implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private BankBillet $bankBillet)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(BankService $bankService): void
    {
        try {
            // Fetch the billet from the bank API
            $billetData = $bankService->getBillet($this->bankBillet->code);

            if (!$billetData) {
                Log::warning('Failed to fetch bank billet barcode', [
                    'bank_billet_id' => $this->bankBillet->id,
                    'code' => $this->bankBillet->code,
                ]);
                return;
            }

            // Update the barcode if it exists in the response
            if (isset($billetData['barcode'])) {
                $this->bankBillet->update([
                    'barcode' => $billetData['barcode'],
                    'bank_response' => $billetData,
                ]);

                Log::info('Bank billet barcode fetched successfully', [
                    'bank_billet_id' => $this->bankBillet->id,
                    'code' => $this->bankBillet->code,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error fetching bank billet barcode', [
                'bank_billet_id' => $this->bankBillet->id,
                'code' => $this->bankBillet->code,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to trigger job retry
            throw $e;
        }
    }
}
