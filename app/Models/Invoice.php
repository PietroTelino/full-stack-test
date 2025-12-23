<?php

namespace App\Models;

use App\Models\Invoice\InvoiceCode;
use App\Models\Invoice\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lib\Tenancy\Tenantable;

class Invoice extends Model
{
    use HasFactory,
        SoftDeletes,
        Tenantable;

    protected $fillable = [
        'customer_id',
        'code',
        'amount',
        'status',
        'issue_date',
        'due_date',
        'payment_date',
        'metadata',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'metadata' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $invoice->code = InvoiceCode::generate($invoice->customer_id);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function bankBillet()
    {
        return $this->hasOne(BankBillet::class);
    }

    public static function calculateItemsTotal(array $items): float
    {
        return collect($items)->sum(function (array $item): float {
            return $item['quantity'] * $item['unit_price'];
        });
    }

    public function syncItems(array $items): void
    {
        $this->invoiceItems()->delete();

        foreach ($items as $item) {
            $this->invoiceItems()->create([
                'title' => $item['title'],
                'subtitle' => $item['subtitle'] ?? null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['quantity'] * $item['unit_price'],
            ]);
        }
    }

    /**
     * Check if the invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now()
            && !$this->payment_date
            && in_array($this->status, ['issued']);
    }

    /**
     * Scope to get overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNull('payment_date')
            ->whereIn('status', ['issued']);
    }
}
