<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'f_name' => 'required|string',
            'l_name' => 'nullable|string',
            'phone' => 'required|string|unique:sellers,phone',
            'image' => 'nullable|image',
            'email' => 'required|unique:sellers,email',
            'password' => 'required|confirmed',
            'bank_name' => 'nullable|string',
            'branch' => 'nullable|string',
            'account_no' => 'nullable|string',
            'holder_name' => 'nullable|string',
            'sales_commission_percentage' => 'nullable|string',
        ];
    }
}
