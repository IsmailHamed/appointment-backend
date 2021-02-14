<?php

namespace App\Http\Requests\Expert;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteExpertRequest extends FormRequest
{
    //ToDo check permission
    public function authorize()
    {
        return Auth::user()->isAdmin();
    }


    public function rules()
    {
        return [
            //
        ];
    }
}
