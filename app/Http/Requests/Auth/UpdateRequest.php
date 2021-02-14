<?php

namespace App\Http\Requests\Auth;

use App\Rules\ValidCurrentUserPassword;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @bodyParam firstName string first name of the user.
 * @bodyParam lastName string first name of the user.
 * @bodyParam email email The email of the user.
 * @bodyParam password string required the account password.
 * @bodyParam image file the user's new image.
 */
class UpdateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {

        return [
            'first_name' => ['max:255'],
            'last_name' => ['max:255'],
            'password' => ['required', 'min:6', 'max:255', new ValidCurrentUserPassword],
            'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
