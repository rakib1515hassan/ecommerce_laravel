<?php

namespace App\Http\Controllers\api\v2\seller\auth;

use App\Models\Seller;
use App\Models\SellerWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\SmsModule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validate = $request->validate([
            'email' => 'required|email|exists:sellers,email',
            'password' => 'required|min:6'
        ]);

        $seller = Seller::where('email', $validate['email'])->first();
        if ($seller && Hash::check($validate['password'], $seller->password)) {
            $token = $seller->createToken('Vendor-App')->accessToken;
            if (!SellerWallet::where('seller_id', $seller->id)->first()) {
                DB::table('seller_wallets')->insert([
                    'seller_id' => $seller->id,
                    'withdrawn' => 0,
                    'commission_given' => 0,
                    'total_earning' => 0,
                    'pending_withdraw' => 0,
                    'delivery_charge_earned' => 0,
                    'collected_cash' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            return $this->successResponse("Successfully Logged in!", [
                'user'  => $seller,
                'access_token' => $token,
            ]);
        }
        return $this->error('Credential doesn\'t match.');
    }

    public function logout()
    {
        Auth::user()->token()->delete();
        return $this->successResponse('Successfully Logout');
    }
}
