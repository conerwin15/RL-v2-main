<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $user = Auth::user();
        if($user != null) {
            $userRoles = $user->getRoleNames();
            $currentRole = $userRoles[0];

            $redirectTo = '/dashboard';
            if($currentRole == 'staff') {
                $redirectTo = '/learning-paths';
            }
            return redirect($userRoles[0] . $redirectTo);
        }

        return $next($request);
    }
}
