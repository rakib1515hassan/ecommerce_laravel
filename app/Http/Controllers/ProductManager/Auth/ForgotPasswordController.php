<?php

namespace App\Http\Controllers\ProductManager\Auth;

use App\Http\Controllers\Controller;
use App\Models\ProductManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:product_manager', ['except' => ['logout']]);
    }



    public function forgot_password()
    {
        return view('product_manager-views.auth.forgot-password');
    }




    public function reset_password_request(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $product_manager = ProductManager::Where(['email' => $request['email']])->first();

        if (isset($product_manager)) {
            $token = Str::random(120);
            DB::table('password_resets')->insert([
                'identity' => $product_manager['email'],
                'token' => $token,
                'created_at' => now(),
            ]);
            $reset_url = url('/') . '/product_manager/auth/reset-password?token=' . $token;
            Mail::to($product_manager['email'])->send(new \App\Mail\PasswordResetMail($reset_url));

            Toastr::success('Check your email. Password reset url sent.');
            return back();
        }

        Toastr::error('No such email found!');
        return back();
    }

    public function reset_password_index(Request $request)
    {
        $data = DB::table('password_resets')->where(['token' => $request['token']])->first();
        if (isset($data)) {
            $token = $request['token'];
            return view('product_manager-views.auth.reset-password', compact('token'));
        }
        Toastr::error('Invalid URL.');
        return redirect('/product_manager/auth/login');
    }




    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8',
        ]);

        $data = DB::table('password_resets')->where(['token' => $request['reset_token']])->first();
        if (isset($data)) {
            DB::table('product_managers')->where(['email' => $data->identity])->update([
                'password' => bcrypt($request['confirm_password'])
            ]);
            Toastr::success('Password reset successfully.');
            DB::table('password_resets')->where(['token' => $request['reset_token']])->delete();
            return redirect('/product_manager/auth/login');
        }
        Toastr::error('Invalid URL.');
        return redirect('/product_manager/auth/login');
    }
}
