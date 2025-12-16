<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\InvoiceOverdueNotification;
use Illuminate\Console\Command;

class SendOverdueInvoiceNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:notify-overdue
                            {--dry-run : Run without sending notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send overdue payment notifications to customers with overdue invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('🔍 Searching for overdue invoices...');
        $this->newLine();

        // Find invoices that are overdue
        $overdueInvoices = Invoice::with('customer')
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->whereNull('payment_date')
            ->get();

        if ($overdueInvoices->isEmpty()) {
            $this->info('✓ No overdue invoices found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$overdueInvoices->count()} overdue invoice(s):");
        $this->newLine();

        $table = [];
        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date);

            $table[] = [
                $invoice->code,
                $invoice->customer->name,
                $invoice->customer->email,
                '$' . number_format($invoice->amount / 100, 2),
                $invoice->due_date->format('Y-m-d'),
                $daysOverdue . ' days',
            ];
        }

        $this->table(
            ['Invoice', 'Customer', 'Email', 'Amount', 'Due Date', 'Days Overdue'],
            $table
        );

        if ($isDryRun) {
            $this->warn('🏃 DRY RUN MODE - No notifications will be sent.');
            return Command::SUCCESS;
        }

        if (!$this->confirm('Do you want to send overdue notifications to these customers?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->info('📧 Sending notifications...');

        $progressBar = $this->output->createProgressBar($overdueInvoices->count());
        $progressBar->start();

        $sent = 0;
        $failed = 0;

        foreach ($overdueInvoices as $invoice) {
            try {
                $invoice->customer->notify(new InvoiceOverdueNotification($invoice));
                $sent++;
                $progressBar->advance();
            } catch (\Exception $e) {
                $failed++;
                $this->error("\nFailed to send notification for invoice {$invoice->code}: {$e->getMessage()}");
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✓ Notifications sent successfully: {$sent}");
        if ($failed > 0) {
            $this->error("✗ Failed notifications: {$failed}");
        }

        return Command::SUCCESS;
    }
}
