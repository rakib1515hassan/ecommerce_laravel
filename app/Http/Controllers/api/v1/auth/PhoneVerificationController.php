<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Services\AdditionalServices;
use App\Services\SmsModule;
use App\Http\Controllers\Controller;
use App\Models\PhoneOrEmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class PhoneVerificationController extends Controller
{
    public function check_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temporary_token' => 'required',
            'phone' => 'required|min:11|max:14'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $user = User::where(['temporary_token' => $request->temporary_token])->first();

        // if (!$user) {
        //     return response()->json(['errors' => "User not found or temporary token mismatch."], 404);
        // }

        if ($user->phone !== $request->phone) {
            return response()->json(['errors' => "Phone number isn't linked to this account."], 403);
        }

        if (isset($user) == false) {
            return response()->json([
                'message' => translate('temporary_token_mismatch'),
            ], 200);
        }

        $token = rand(1000, 9999);
        DB::table('phone_or_email_verifications')->insert([
            'phone_or_email' => $request['phone'],
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $msg = "প্রিয় গ্রাহক, \nআপনার ভেরিফিকেশন কোড টি হল " . $token . " \n ধন্যবাদ,\nshojonsl.com";

        // $response = SmsModule::send($request['phone'], $token);
        $response = SmsModule::sendSms_greenweb($user, $msg);

        // \Log::info("Response =" . $response);

        return response()->json([
            'message' => $response,
            'token' => 'active',
        ], 200);
    }

    public function verify_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'temporary_token' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $verify = PhoneOrEmailVerification::where(['phone_or_email' => $request['phone'], 'token' => $request['otp']])->first();

        if (isset($verify)) {
            try {
                $user = User::where(['temporary_token' => $request['temporary_token']])->first();
                $user->phone = $request['phone'];
                $user->is_phone_verified = 1;
                $user->save();
                $verify->delete();
            } catch (\Exception $exception) {
                return response()->json([
                    'message' => translate('temporary_token_mismatch'),
                ], 200);
            }

            $token = $user->createToken('LaravelAuthApp')->accessToken;
            $is_membership = $user->is_membership;
            $user_id = $user->id;

            return response()->json([
                'message' => translate('otp_verified'),
                'token' => $token,
                'is_membership' => $is_membership,
                'user_id' => $user_id,
            ], 200);
        }

        return response()->json([
            'errors' => [
                ['code' => 'token', 'message' => translate('something_wrong_please_try_again')],
            ]
        ], 404);
    }
}
