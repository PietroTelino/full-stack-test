<?php

namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lib\Tenancy\Tenantable;

class InvoiceItem extends Model
{
    use HasFactory,
        Tenantable;

    protected $fillable = [
        'invoice_id',
        'title',
        'subtitle',
        'amount',
        'quantity',
        'unit_price',
    ];
}
