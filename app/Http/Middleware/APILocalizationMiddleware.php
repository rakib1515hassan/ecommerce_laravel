<?php

namespace App\Http\Middleware;

use App\Services\AdditionalServices;
use Closure;
use Illuminate\Support\Facades\App;

class APILocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $local = ($request->hasHeader('lang')) ? (strlen($request->header('lang')) > 0 ? $request->header('lang') : AdditionalServices::default_lang()) : AdditionalServices::default_lang();
        App::setLocale($local);
        return $next($request);
    }
}
