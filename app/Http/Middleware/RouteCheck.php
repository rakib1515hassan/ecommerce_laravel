<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class RouteCheck
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
        return $next($request);


        if (!Route::is('seller*') && !Route::is('product_manager*') && !Route::is('reseller*') && !Route::is('admin*') && !Route::is('api*') && !Route::is('customer.auth*')) {
            // check is localhost
            if ($request->getHost() == 'localhost' || $request->getHost() == '127.0.0.1') {

                return $next($request);
            }

            abort(404);
        }

        return $next($request);
    }
}
