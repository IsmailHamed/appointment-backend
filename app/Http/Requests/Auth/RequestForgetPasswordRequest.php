<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam email email required The email of the user.
 */
class RequestForgetPasswordRequest extends FormRequest
{


    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'email' => ['required','exists:users,email'],
        ];
    }
}
