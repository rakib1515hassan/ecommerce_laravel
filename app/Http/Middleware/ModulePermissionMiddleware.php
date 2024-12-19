<?php

namespace App\Http\Middleware;

use App\Services\AdditionalServices;
use Brian2694\Toastr\Facades\Toastr;
use Closure;

class ModulePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module)
    {
        if (AdditionalServices::module_permission_check($module)) {
            return $next($request);
        }

        Toastr::error('Access Denied !');
        return back();
    }
}
