<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class PassportAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            // 'password' => 'required|min:8',
            'password' => [
                'required',
                'min:8',
                // 'regex:/[!@#$%^&*(),.?":{}|<>]/',
                'regex:/[a-zA-Z]/'
            ],
            'fcm_device_token' => 'nullable|string'
        ], [
            'f_name.required' => 'The first name field is required.',
            'l_name.required' => 'The last name field is required.',
            'password' => 'Password must be contain at least 8 characters and one letter.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $temporary_token = Str::random(40);
        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => 1,
            'password' => bcrypt($request->password),
            'temporary_token' => $temporary_token,
        ]);

        // Save the FCM token if provided
        if ($request->has('fcm_device_token')) {
            $user->cm_firebase_token = $request->fcm_device_token;
        }
        $user->save();

        $phone_verification = AdditionalServices::get_business_settings('phone_verification');
        $email_verification = AdditionalServices::get_business_settings('email_verification');

        if ($phone_verification && !$user->is_phone_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        if ($email_verification && !$user->is_email_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }

        $token = $user->createToken('LaravelAuthApp')->accessToken;
        $is_membership = $user->is_membership;
        $user_id = $user->id;
        return response()->json([
            'token' => $token,
            'is_membership' => $is_membership,
            'user_id' => $user_id,
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6',
            'fcm_device_token' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $user_id = $request['email'];
        if (filter_var($user_id, FILTER_VALIDATE_EMAIL)) {
            $medium = 'email';
        } else {
            $count = strlen(preg_replace("/[^\d]/", "", $user_id));
            if ($count >= 9 && $count <= 15) {
                $medium = 'phone';
            } else {
                $errors = [];
                array_push($errors, ['code' => 'email', 'message' => 'Invalid email address or phone number']);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
        }

        $data = [
            $medium => $user_id,
            'password' => $request->password
        ];

        $user = User::where([$medium => $user_id])->first();

        if (isset($user) && $user->is_active && auth()->attempt($data)) {
            $user->temporary_token = Str::random(40);

            // Save the FCM token if provided
            if ($request->has('fcm_device_token')) {
                $user->cm_firebase_token = $request->fcm_device_token;
            }

            $user->save();

            $phone_verification = AdditionalServices::get_business_settings('phone_verification');
            $email_verification = AdditionalServices::get_business_settings('email_verification');
            if ($phone_verification && !$user->is_phone_verified) {
                return response()->json(['temporary_token' => $user->temporary_token], 200);
            }
            if ($email_verification && !$user->is_email_verified) {
                return response()->json(['temporary_token' => $user->temporary_token], 200);
            }

            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            $is_membership = $user->is_membership;
            $user_id = $user->id;

            // $response = [
            //     "token" => $token,
            //     "is_membership" => $is_membership
            // ];

            return response()->json([
                'token' => $token,
                'is_membership' => $is_membership,
                'user_id' => $user_id,
            ], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => translate('Customer_not_found_or_Account_has_been_suspended')]);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }

    // public function change_password(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'old_password' => 'required',
    //         'new_password' => 'required|min:8',
    //         'confirm_password' => 'required|same:new_password',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
    //     }

    //     $user = auth()->user();
    //     if (isset($user) && $user->is_active && auth()->attempt(['email' => $user->email, 'password' => $request->old_password])) {
    //         $user->password = bcrypt($request->new_password);
    //         $user->save();
    //         return response()->json(['message' => translate('Password_changed_successfully')], 200);
    //     } else {
    //         $errors = [];
    //         $errors[] = ['code' => 'auth-001', 'message' => translate('Customer_not_found_or_Account_has_been_suspended')];
    //         return response()->json([
    //             'errors' => $errors
    //         ], 401);
    //     }
    // }


    public function change_password(Request $request)
    {
        // $user = $request->user();
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['error' => 'Your account is inactive.'], 403);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['error' => 'The old password is incorrect.'], 403);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'old_password' => 'required',
                // 'new_password' => 'required|min:8',
                'new_password' => [
                    'required',
                    'min:8',
                    // 'regex:/[!@#$%^&*(),.?":{}|<>]/'
                    'regex:/[a-zA-Z]/'
                ],
            ],
            [
                'new_password' => 'Password must be contain at least 8 characters and one letter.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => AdditionalServices::error_processor($validator)], 403);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        // return response()->json(['message' => translate('Password_changed_successfully')], 200);

        $response = [
            'status' => "success",
            'message' => translate('Password_changed_successfully'),
        ];

        return response()->json($response, 200);
    }

}
