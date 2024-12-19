<?php

namespace App\Http\Controllers\api\v2\seller\auth;

use App\Models\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $seller = new Seller();
        $seller = $seller->forceFill($data);
        $seller->save();
        return $this->successResponse('Registration Successfully!', $seller);
    }
}
