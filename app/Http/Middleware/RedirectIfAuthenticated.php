<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        switch ($guard) {
            case 'admin':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('admin.dashboard');
                }
                break;
            case 'seller':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('seller.dashboard.index');
                }
                break;
            case 'product_manager':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('product_manager.dashboard.index');
                }
                break;
            case 'reseller':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('reseller.dashboard.index');
                }
                break;
            case 'customer':
                if (Auth::guard($guard)->check()) {
                    return redirect()->route('admin.dashboard');
                }
                break;
            default:
                if (Auth::guard($guard)->check()) {
                    return redirect('home');
                }
                break;
        }

        return $next($request);
    }
}
