<?php

namespace App\Http\Requests\Expert;

use App\Enums\Countries;
use DateTimeZone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateExpertRequest extends FormRequest
{

    public function authorize()
    {
        return Auth::user()->isAdmin();
    }


    public function rules()
    {
        $timeZones = DateTimeZone::listIdentifiers();
        $countries = Countries::getValues();
        return [
            'first_name' => ['max:255'],
            'last_name' => ['max:255'],
            'password' => ['min:6', 'max:255'],
            'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'job' => ['max:255'],
            'country' => [Rule::in($countries)],
            'time_zone' => [Rule::in($timeZones)],

        ];
    }
}
