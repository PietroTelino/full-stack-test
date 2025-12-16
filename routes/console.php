<?php

use App\Console\Commands\SendOverdueInvoiceNotifications;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendOverdueInvoiceNotifications::class)->dailyAt('9:00');
