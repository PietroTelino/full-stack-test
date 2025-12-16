<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    Lib\Billing\BillingServiceProvider::class,
    Lib\Tenancy\TenancyServiceProvider::class,
];
