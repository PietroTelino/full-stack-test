<?php

use Lib\Billing\Http\Controllers\BillingPortalController;
use Lib\Billing\Http\Controllers\CheckoutController;
use Lib\Billing\Http\Controllers\CheckoutReturnController;
use Lib\Billing\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::as('billing.')->group(function () {
    Route::get('products', ProductController::class)->name('products');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('portal', BillingPortalController::class)->name('portal');
        Route::get('checkout/return/', CheckoutReturnController::class)->name('checkout.return');
        Route::get('checkout/{lookup_key}', CheckoutController::class)->name('checkout');
    });
});
