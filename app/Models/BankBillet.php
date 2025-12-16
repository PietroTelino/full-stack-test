<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lib\Tenancy\Tenantable;

class BankBillet extends Model
{
    use HasFactory,
        SoftDeletes,
        Tenantable;

    protected $fillable = [
        'invoice_id',
        'code',
        'barcode',
        'bank_response',
        'expires_at',
    ];

    protected $casts = [
        'bank_response' => 'array',
        'expires_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
