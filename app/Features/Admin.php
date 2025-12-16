<?php

namespace App\Features;

use App\Feature;
use Illuminate\Support\Facades\Event;
use Lib\Tenancy\Tenant;

class Admin extends Feature
{
    public function prepareForUsage()
    {
        Tenant::asLandlord();
    }

    public static function register()
    {
        // --------------------------------------------------------------
        // To impersonate a user using sanctum, we need to store the
        // password hash in the session.
        //
        // see: https://github.com/404labfr/laravel-impersonate/issues/162
        // --------------------------------------------------------------

        Event::listen(function (\Lab404\Impersonate\Events\TakeImpersonation $event) {
            // Build out the impersonation event listeners -
            // Otherwise we get a redirect to login if not setting the password_hash_sanctum when using sanctum.
            session()->put('password_hash_sanctum', $event->impersonated->getAuthPassword());
        });

        Event::listen(\Lab404\Impersonate\Events\LeaveImpersonation::class, function (\Lab404\Impersonate\Events\LeaveImpersonation $event) {
            // Restore the password hash for the impersonator.
            session()->remove('password_hash_web');
            session()->put(['password_hash_sanctum' => $event->impersonator->getAuthPassword()]);
            auth()->setUser($event->impersonator);
        });
    }
}
