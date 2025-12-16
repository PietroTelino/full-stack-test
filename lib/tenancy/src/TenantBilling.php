<?php

namespace Lib\Tenancy;

use App\Features;
use Laravel\Cashier\Subscription;
use Spatie\LaravelData\Data;

class TenantBilling extends Data
{
    public function __construct(
        public readonly bool $never_subscribed,
        public readonly bool $valid_subscriptions,
        public readonly bool $has_any_subscription,
        public readonly bool $has_any_valid_subscription,
        public readonly bool $on_trial,
        public readonly bool $access_blocked,
        public readonly bool $has_incomplete_payment,
    ) {}

    public static function billing()
    {
        $webmaster = Features::check(request(), 'admin');
        if ($owner = Tenant::owner()) {
            return cache()->remember(sprintf('team::%s::billing_props::%s', $owner->id, $owner->updated_at), now()->addMinutes(5), function () use ($owner) {
                return [
                    'never_subscribed' => $owner->neverSubscribed(),
                    'valid_subscriptions' => $owner->validSubscriptions(),
                    'has_any_subscription' => $owner->hasAnySubscription(),
                    'has_any_valid_subscription' => $owner->hasAnyValidSubscription(),
                    'on_trial' => $owner->onTrial(),
                    'access_blocked' => $owner->isAccessBlocked(),
                    'has_incomplete_payment' => $owner->getSubscriptions()->some(fn (Subscription $sub) => $sub->hasIncompletePayment()),
                ];
            });
        } else {
            return [
                'never_subscribed' => $webmaster,
                'valid_subscriptions' => $webmaster,
                'has_any_subscription' => $webmaster,
                'has_any_valid_subscription' => $webmaster,
                'on_trial' => ! $webmaster,
                'access_blocked' => ! $webmaster,
                'has_incomplete_payment' => ! $webmaster,
            ];
        }
    }
}
