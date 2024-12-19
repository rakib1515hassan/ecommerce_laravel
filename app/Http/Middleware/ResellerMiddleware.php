<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ResellerMiddleware
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
        if (auth('reseller')->check() && auth('reseller')->user()->is_active == '1') {
            return $next($request);
        }
        auth()->guard('reseller')->logout();
        return redirect()->route('reseller.auth.login');
    }
}
