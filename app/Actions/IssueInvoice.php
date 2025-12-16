<?php

namespace App\Actions;

use App\Jobs\IssueInvoiceJob;
use App\Models\Invoice;
use App\Services\BankService;

class IssueInvoice
{
    protected BankService $bankService;

    public function __construct(BankService $bankService)
    {
        $this->bankService = $bankService;
    }

    public function execute(Invoice $invoice)
    {
        if ($invoice->status === 'issued') {
            throw new \Exception('Invoice is already issued');
        }

        if (!$invoice->due_date) {
            throw new \Exception('Invoice must have a due date to be issued');
        }

        if (!$invoice->amount || $invoice->amount <= 0) {
            throw new \Exception('Invoice must have a valid amount to be issued');
        }

        // Update invoice status
        $invoice->update([
            'status' => 'issued',
            'issue_date' => now(),
        ]);

        IssueInvoiceJob::dispatch($invoice);
    }
}
