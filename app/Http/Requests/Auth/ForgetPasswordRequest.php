<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam email email required The email of the user.
 * @bodyParam code string the OTP code form email.
 * @bodyParam password string the desired password.
 */
class ForgetPasswordRequest extends FormRequest
{


    public function authorize()
    {
        return true;
    }


    public function rules()
    {

        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => 'required',
            'password' => ['required', 'min:6'],
        ];
    }
}
