<?php

namespace Lib\Billing\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Checkout intent means the user may want subscribe to our product
 */
class CheckoutIntent
{
    use Dispatchable;

    public function __construct(private User $user) {}

    public function eventType(): string
    {
        return 'Checkout Intent';
    }

    public function properties(): array
    {
        return [
            'intention_days' => $this->user->created_at->diffInDays(now()),
        ];
    }
}
