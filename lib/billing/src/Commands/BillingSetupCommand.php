<?php

namespace Lib\Billing\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Laravel\Cashier\Cashier;
use RuntimeException;
use Stripe;

class BillingSetupCommand extends Command
{
    protected $signature = 'billing:setup';

    protected $description = 'Setup your Stripe account.';

    public function handle()
    {
        throw_if(empty(env('STRIPE_KEY')), RuntimeException::class, 'Missing STRIPE_KEY environment variable. Go to https://dashboard.stripe.com/apikeys and copy it\'s value from Publishable key.');
        throw_if(empty(env('STRIPE_SECRET')), RuntimeException::class, 'Missing STRIPE_SECRET environment variable. Go to https://dashboard.stripe.com/apikeys and copy it\'s value from Secret key.');

        if (! devOnly()) {
            return;
        }

        $this->setupPlans();
    }

    private function setupPlans()
    {
        $this->info('> Setting up Stripe products');

        collect(config('billing.products'))->each(function ($productData) {
            $stripeProduct = $this->findOrCreateStripeProduct($productData);
            collect($productData['prices'])->each(function ($priceData) use ($stripeProduct) {
                $this->findOrCreateStripePrice($priceData, $stripeProduct);
            });
        });
    }

    private function findOrCreateStripeProduct(array $productData): Stripe\Product
    {
        $remoteProducts = collect(Cashier::stripe()->products->all()->data);

        if ($stripeProduct = $remoteProducts->where('name', $productData['name'])->first()) {
            return $stripeProduct;
        }

        return Cashier::stripe()->products->create(Arr::except($productData, 'prices'));
    }

    private function findOrCreateStripePrice($priceData, Stripe\Product $stripeProduct): void
    {
        $stripePrices = collect(Cashier::stripe()->prices->all()->data);

        $remotePrice = $stripePrices->where('product', $stripeProduct->id)
            ->where('recurring.interval', $priceData['recurring']['interval'])
            ->first();

        if (! $remotePrice) {
            Cashier::stripe()->prices->create([
                'product' => $stripeProduct->id,
                ...$priceData,
            ]);
        }
    }
}
