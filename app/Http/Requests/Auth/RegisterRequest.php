<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam firstName string required first name of the user.
 * @bodyParam lastName string required first name of the user.
 * @bodyParam email email required The email of the user.
 * @bodyParam password string required the desired password.
 * @bodyParam image file The image.
 * @bodyParam _method string This is a problem with PHP.Example: PUT
 * @package App\Http\Requests\Auth
 */
class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:6', 'max:255'],
            'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
