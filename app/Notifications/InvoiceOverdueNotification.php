<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Invoice $invoice
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysOverdue = now()->diffInDays($this->invoice->due_date);

        return (new MailMessage)
            ->subject('Payment Overdue - Invoice ' . $this->invoice->code)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that payment for invoice **' . $this->invoice->code . '** is overdue.')
            ->line('**Invoice Details:**')
            ->line('Invoice Code: ' . $this->invoice->code)
            ->line('Amount Due: $' . number_format($this->invoice->amount / 100, 2))
            ->line('Due Date: ' . $this->invoice->due_date->format('F j, Y'))
            ->line('Days Overdue: ' . $daysOverdue . ' days')
            ->action('View Invoice', url('/invoices/' . $this->invoice->id))
            ->line('Please arrange payment as soon as possible to avoid any service interruption.')
            ->salutation('Best regards, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_code' => $this->invoice->code,
            'amount' => $this->invoice->amount,
            'due_date' => $this->invoice->due_date,
        ];
    }
}
