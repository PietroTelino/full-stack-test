<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Lib\Tenancy\Tenantable;

class Customer extends Model
{
    use HasFactory,
        Notifiable,
        Tenantable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
