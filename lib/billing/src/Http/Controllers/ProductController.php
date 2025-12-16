<?php

namespace Lib\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Lib\Billing\Events\CheckoutIntent;
use Illuminate\Support\Arr;
use Laravel\Cashier\Cashier;
use Lib\Tenancy\TenantBilling;

class ProductController extends Controller
{
    public function __invoke()
    {
        $hasIncompletePayment = false;
        $hasSubscription = false;

        if (auth()->check()) {
            /** @var User $user */
            $user = auth()->user();
            CheckoutIntent::dispatch($user);

            $hasIncompletePayment = Arr::get(TenantBilling::billing(), 'has_incomplete_paylment', false);
            $hasSubscription = Arr::get(TenantBilling::billing(), 'has_any_valid_subscription', false);
        }

        return inertia('Products', [
            'has_subscription' => $hasSubscription,
            'has_incomplete_payment' => $hasIncompletePayment,
            'products' => collect(cache()->remember(
                'stripe.products.all',
                now()->addMinutes(5),
                fn () => Cashier::stripe()->products->all(['active' => true, 'expand' => ['data.prices']])
            ))->map(fn ($stripeProduct) => [
                'name' => $stripeProduct->name,
                'id' => $stripeProduct->id,
                'description' => $stripeProduct->description,
                'features' => $stripeProduct->features,
                'most_popular' => config('billing.product_name_map')[$stripeProduct->name] === 'crm',
                'prices' => collect(cache()->remember(
                    'stripe.prices.all::'.$stripeProduct->id,
                    now()->addMinutes(5),
                    fn () => Cashier::stripe()->prices->all(['product' => $stripeProduct->id, 'active' => true]))
                )->reduce(function ($acc, $stripePrice) {
                    $acc[$stripePrice->recurring->interval] = [
                        // ...$stripePrice,
                        'amount_discount' => 'R$'.number_format($stripePrice->unit_amount / 100, 2, ',', '.'),
                        'amount' => 'R$'.number_format($stripePrice->unit_amount * 2 / 100, 2, ',', '.'),
                        'href' => route('billing.checkout', $stripePrice->id),
                    ];

                    return $acc;
                }, []),
            ])->sortBy(fn ($productData) => match ($productData['name']) {
                'Tudo incluso' => 1,
                default => 0,
            })->values(),
        ]);
    }
}
