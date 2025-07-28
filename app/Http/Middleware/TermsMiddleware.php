<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class TermsMiddleware
{

    protected $except = ['terms-conditions', 'logout'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if($user) {

            $shouldSkip = false;
            foreach ($this->except as $excluded_route) {
                if (Str::contains($request->path(), $excluded_route)) {
                    $shouldSkip = true; // if route belongs to skip route
                    break;
                }
            }


            if(!$shouldSkip && !$user->terms_accepted) {
                $role = $user->roles[0]->name;
                return redirect($role . '/terms-conditions');
            }
        }
        
        return $next($request);
    }
}
