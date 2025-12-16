<?php

namespace Lib\Billing;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BillingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerRoutes();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}

    protected function registerRoutes()
    {
        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {
            return;
        }

        Route::group([
            'prefix' => 'billing',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }
}
