<?php

namespace App\Http\Middleware;

use App\Models\Reseller;
use Closure;
use functiontranslate;

class ResellerAuth
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
        $token = explode(' ', $request->header('authorization'));
        if (count($token) > 1 && strlen($token[1]) > 30) {
            $p_man = Reseller::where(['auth_token' => $token['1']])->first();
            if (isset($p_man)) {
                $request['reseller'] = $p_man;
                return $next($request);
            }
        }

        return response()->json([
            'auth-001' => translate('Your existing session token does not authorize you any more')
        ], 401);
    }
}