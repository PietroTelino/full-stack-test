<?php

namespace App\Jobs;

use App\Models\BankBillet;
use App\Models\Invoice;
use App\Services\BankService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class IssueInvoiceJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Invoice $invoice)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(BankService $bankService): void
    {
        $invoice = $this->invoice;
        DB::transaction(function () use ($invoice, $bankService) {
            // Generate bank billet through bank service
            $billetData = $bankService->generateBillet(
                $invoice->id,
                $invoice->customer,
                $invoice->amount,
                $invoice->due_date
            );

            if (empty($billetData['code'])) {
                throw new \Exception('Bank service did not return a billet code');
            }

            // Create bank billet record
            $bankBillet = BankBillet::create([
                'invoice_id' => $invoice->id,
                'code' => $billetData['code'],
                'bank_response' => $billetData['bank_response'] ?? null,
                'expires_at' => $billetData['expires_at'] ?? $invoice->due_date,
            ]);

            // Dispatch job to fetch the barcode later (simulating async barcode generation)
            FetchBankBilletBarcode::dispatch($bankBillet)->delay(now()->addSeconds(5));

            return $bankBillet;
        });
    }
}
