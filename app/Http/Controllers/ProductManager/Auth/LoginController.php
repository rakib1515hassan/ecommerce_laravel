<?php

namespace App\Http\Controllers\ProductManager\Auth;

use App\Http\Controllers\Controller;
use App\Models\ProductManager;
use App\Models\ProductManagerWallet;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Gregwar\Captcha\CaptchaBuilder;
use App\Services\AdditionalServices;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:product_manager', ['except' => ['logout']]);
    }

    public function login()
    {
        $custome_recaptcha = new CaptchaBuilder;
        $custome_recaptcha->build();
        Session::put('custome_recaptcha', $custome_recaptcha->getPhrase());
        return view('product_manager-views.auth.login', compact('custome_recaptcha'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        //recaptcha validation
        $recaptcha = AdditionalServices::get_business_settings('recaptcha');
        if (isset($recaptcha) && $recaptcha['status'] == 1) {
            try {
                $request->validate([
                    'g-recaptcha-response' => [
                        function ($attribute, $value, $fail) {
                            $secret_key = AdditionalServices::get_business_settings('recaptcha')['secret_key'];
                            $response = $value;
                            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response;
                            $response = \file_get_contents($url);
                            $response = json_decode($response);
                            if (!$response->success) {
                                $fail(translate('ReCAPTCHA Failed'));
                            }
                        },
                    ],
                ]);
            } catch (\Exception $exception) {
            }
        } else if ($recaptcha['status'] == 0) {
            $builder = new CaptchaBuilder();
            $builder->setPhrase(session()->get('custome_recaptcha'));
            if (!$builder->testPhrase($request->custome_recaptcha)) {
                Toastr::error(translate('ReCAPTCHA Failed'));
                return back();
            }
        }

        $se = ProductManager::where(['email' => $request['email']])->first(['is_active']);
        if (isset($se) && $se['is_active'] == '1' && auth('product_manager')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            Toastr::info('Welcome to your dashboard!');
            return redirect()->route('product_manager.dashboard.index');
        } else if (isset($se) && $se['is_active'] == '0') {
            return redirect()->back()->withInput($request->only('email', 'remember'))
                ->withErrors(['Your account is not active.']);
        }

        return redirect()->back()->withInput($request->only('email', 'remember'))
            ->withErrors(['Credentials does not match.']);
    }


    public function logout(Request $request)
    {
        auth()->guard('product_manager')->logout();

        $request->session()->invalidate();

        return redirect()->route('product_manager.auth.login');
    }
}
