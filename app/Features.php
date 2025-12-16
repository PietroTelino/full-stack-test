<?php

namespace App;

use App\Models\User;
use App\Features\Admin;

class Features
{
    public static function boot(User $user)
    {
        $features = $user->features ?? [];
        $default = [];

        // Check if the user is a webmaster, if so, add the admin and impersonate features
        if (collect(config('webmasters'))->map->mail->contains($user->email) || app('impersonate')->isImpersonating()) {
            $default[] = 'admin';
        }

        // Remove any features that are not in the default list
        return collect($default)->merge(
            array_intersect($default, $features)
        )->unique()->toArray();
    }

    /**
     * Determine if the given request has access to the feature.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string  $name Feature name
     * @return bool
     */
    public static function check($request, string $name)
    {
        return $request->user()?->hasFeature($name);
    }

    /**
     * Prepare the feature for usage.
     *
     * @param \Illuminate\Http\Request  $request
     * @param string  $name Feature name
     */
    public static function prepareForUsage($request, string $name)
    {
        match ($name) {
            'admin' => (new Admin)->prepareForUsage($request),
            default => null,
        };
    }
}
