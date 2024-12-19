<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdditionalServices;
use App\Services\SmsModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Controllers\api\v1\EmailVerificationController;


class ForgotPassword extends Controller
{
    public function reset_password_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $verification_by = AdditionalServices::get_business_settings('forgot_password_verification');
        DB::table('password_resets')->where('identity', 'like', "%{$request['identity']}%")->delete();

        if ($verification_by == 'email') {
            $customer = User::Where(['email' => $request['identity']])->first();

            if (isset($customer)) {
                // \Log::info("Email =". $customer->email);
                $token = rand(100000, 999999);

                DB::table('password_resets')->insert([
                    'identity' => $customer->email,
                    'token' => $token,
                    'created_at' => now(),
                ]);

                try {
                    \Log::info("Email =" . $customer->email);
                    $mailResponse = Mail::to($customer->email)->send(new \App\Mail\EmailVerification($token));
                    $response = translate('check_your_email');
                } catch (\Exception $exception) {
                    $response = translate('email_failed! Please try again.');
                }

                return response()->json([
                    'message' => $response,
                ], 200);

            }


        } elseif ($verification_by == 'phone') {
            // \Log::info("Identity = " . $request->identity);

            $customer = User::where('phone', 'like', "%{$request['identity']}%")->first();

            if (isset($customer)) {
                // \Log::info("Identity = " . $customer->phone);

                $token = rand(100000, 999999);
                DB::table('password_resets')->insert([
                    'identity' => $customer['phone'],
                    'token' => $token,
                    'created_at' => now(),
                ]);

                // SmsModule::sendSms_greenweb($customer->phone, $token);
                $msg = "প্রিয় গ্রাহক, \nআপনার ভেরিফিকেশন কোড টি হল " . $token . " \n ধন্যবাদ,\nshojonsl.com";
                $response = SmsModule::sendSms_greenweb($customer, $msg);
                \Log::info($response);
                return response()->json(['message' => 'otp sent successfully.'], 200);
            }
        }
        return response()->json([
            'errors' => [
                ['code' => 'not-found', 'message' => 'user not found!']
            ]
        ], 404);
    }

    public function otp_verification_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $id = session('forgot_password_identity');
        $data = DB::table('password_resets')->where(['token' => $request['otp']])
            ->where('identity', 'like', "%{$id}%")
            ->first();

        if (isset($data)) {
            return response()->json(['message' => 'otp verified.'], 200);
        }

        return response()->json([
            'errors' => [
                ['code' => 'not-found', 'message' => 'invalid OTP']
            ]
        ], 404);
    }



    public function reset_password_set(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required',
            'password' => 'required|same:confirm_password|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $data = DB::table('password_resets')
            ->where('identity', 'like', "%{$request['identity']}%")
            ->where(['token' => $request['otp']])->first();

        // \Log::info($data);
        $verification_by = AdditionalServices::get_business_settings('forgot_password_verification');

        if (isset($data)) {
            if ($verification_by == 'email') {
                DB::table('users')->where('email', 'like', "%{$data->identity}%")
                    ->update([
                        'password' => bcrypt(str_replace(' ', '', $request['password']))
                    ]);

                DB::table('password_resets')
                    ->where('identity', 'like', "%{$request['identity']}%")
                    ->where(['token' => $request['otp']])->delete();

                return response()->json(['message' => 'Password changed successfully.'], 200);

            } elseif ($verification_by == 'phone') {
                DB::table('users')->where('phone', 'like', "%{$data->identity}%")
                    ->update([
                        'password' => bcrypt(str_replace(' ', '', $request['password']))
                    ]);

                DB::table('password_resets')
                    ->where('identity', 'like', "%{$request['identity']}%")
                    ->where(['token' => $request['otp']])->delete();

                return response()->json(['message' => 'Password changed successfully.'], 200);
            }
        }
        return response()->json([
            'errors' => [
                ['code' => 'invalid', 'message' => 'Invalid token.']
            ]
        ], 400);
    }
}
