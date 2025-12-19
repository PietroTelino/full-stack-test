<?php

use Illuminate\Support\Facades\Route;

// Landing page: envia o usuário direto para a área "real" do app.
// Se não estiver autenticado, o middleware do /dashboard vai redirecionar para login.
Route::redirect('/', '/dashboard')->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function() {
    Route::impersonate();

    Route::prefix('a/admin')
        ->name('admin.')
        ->middleware(\App\Http\Middleware\FeatureAuthorizationMiddleware::class . ':admin')
        ->group(base_path('routes/admin.php'));

    Route::get('dashboard', fn() => inertia('Dashboard'))->name('dashboard');

    Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
    Route::post('invoices/{invoice}/issue', [\App\Http\Controllers\InvoiceController::class, 'issue'])
        ->name('invoices.issue');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
});

require __DIR__.'/settings.php';
