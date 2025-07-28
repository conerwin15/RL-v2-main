<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Auth;
use App;
use App\Models\UserLanguage;
use Illuminate\Http\Request;

class ApiLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $existUserLang = UserLanguage::where('user_id', $user->id)->first();
        if(!is_null($existUserLang) && $existUserLang->lang_code != '')
        {
            App::setLocale($existUserLang->lang_code);
        } 
        
        return $next($request);
    }
}
