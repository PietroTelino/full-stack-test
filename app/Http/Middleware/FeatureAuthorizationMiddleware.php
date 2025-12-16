<?php

namespace App\Http\Middleware;

use Closure;
use App\Features;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureAuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $name): Response
    {
        if (! Features::check($request, $name)) {
            abort(403, 'Unauthorized action.');
        }

        Features::prepareForUsage($request, $name);

        return $next($request);
    }
}
