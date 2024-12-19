<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\PhoneOrEmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class EmailVerificationController extends Controller
{
    public function check_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temporary_token' => 'required',
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            \Log::info('Invalid');
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $user = User::where('email', $request->email)->first();
        // \Log::info('Request User:'. $user);

        if (!$user || $user->temporary_token != $request->temporary_token) {
            return response()->json([
                'message' => translate('temporary_token_mismatch'),
            ], 200);
        }

        $token = rand(1000, 9999);
        DB::table('phone_or_email_verifications')->insert([
            'phone_or_email' => $request['email'],
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            // $respone = Mail::to($request['email'])->send(new \App\Mail\EmailVerification($token));
            $mailResponse = Mail::to($request['email'])->send(new \App\Mail\EmailVerification($token));
            // \Log::info('Email Response:' . $mailResponse);

            $response = translate('check_your_email');
        } catch (\Exception $exception) {
            // \Log::info('Email Response:' . $exception);

            $response = translate('email_failed');
        }

        return response()->json([
            'message' => $response,
            'token' => 'active'
        ], 200);
    }


    public function verify_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'temporary_token' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $verify = PhoneOrEmailVerification::where(['phone_or_email' => $request['email'], 'token' => $request['token']])->first();

        if ($verify) {
            try {
                $user = User::where('temporary_token', $request['temporary_token'])->first();

                if (!$user) {
                    return response()->json([
                        'message' => translate('User not found for the provided temporary token'),
                    ], 404);
                }

                $user->email = $request['email'];
                $user->is_email_verified = 1;
                $user->save();

                $verify->delete();

                $token = $user->createToken('LaravelAuthApp')->accessToken;
                $is_membership = $user->is_membership;
                $user_id = $user->id;

                return response()->json([
                    'message' => translate('OTP verified'),
                    'token' => $token,
                    'is_membership' => $is_membership,
                    'user_id' => $user_id,
                ], 200);

            } catch (\Exception $exception) {
                return response()->json([
                    'message' => translate('An error occurred while verifying the email.'),
                    'error' => $exception->getMessage()
                ], 400);
            }
        }

        return response()->json([
            'errors' => [
                ['code' => 'token', 'message' => translate('Invalid token')]
            ]
        ], 400);
    }

}
