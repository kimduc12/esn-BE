<?php

namespace App\Http\Middleware;

use Closure;

class LocalizationMiddleware
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
        //Check header request and set language defaut

        //Set laravel localization
        app()->setLocale($request->header('Content-Language') ?? 'vi');
        //Continue request
        return $next($request);
    }
}
