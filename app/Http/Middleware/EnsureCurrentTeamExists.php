<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCurrentTeamExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // If the user has no current team, switch to the first available team.
            // This scenario can happen when the user is logged in a team and the team is deleted.
            if (is_null(Auth::user()->current_team_id)) {
                $team = Auth::user()->allTeams()->first();

                if ($team) {
                    Auth::user()->switchTeam($team);
                }
            }
        }

        return $next($request);
    }
}
