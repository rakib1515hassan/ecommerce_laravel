<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ProductManagerMiddleware
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
        if (auth('product_manager')->check() && auth('product_manager')->user()->is_active == '1') {
            return $next($request);
        }
        auth()->guard('product_manager')->logout();
        return redirect()->route('product_manager.auth.login');
    }
}
