<?php

namespace App\Http\Middleware;

use Closure;

class Localization
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        $locale = $request->header('hl');

        if ( $locale and in_array($locale, config('app.locales')) ) {
            app()->setLocale($locale);            
        }
        return $next($request);
    }
}
