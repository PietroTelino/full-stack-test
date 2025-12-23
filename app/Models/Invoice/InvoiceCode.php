<?php

namespace App\Models\Invoice;

use App\Models\Invoice;
use Hidehalo\Nanoid\Client;

class InvoiceCode
{
    public static function generate(int $customerId, int $increment = 1): string
    {
        $count = Invoice::withTrashed()
            ->where('customer_id', $customerId)
            ->count('customer_id') + $increment;

        $code = sprintf(
            '%s-%s',
            (new Client)->formatedId(alphabet: 'ABCDE', size: 5),
            str_pad((string) $count, 4, '0', STR_PAD_LEFT)
        );

        return $code;
    }
}
