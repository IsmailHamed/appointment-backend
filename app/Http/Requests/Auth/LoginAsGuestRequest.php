<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @bodyParam email email required The email of the user.
 * @bodyParam password string the desired password.
 */
class LoginAsGuestRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'uuid' => ['required', 'uuid'],
            'first_name' => ['required', 'max:255'],

        ];
    }
}
