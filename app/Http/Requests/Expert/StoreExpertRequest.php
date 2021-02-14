<?php

namespace App\Http\Requests\Expert;

use App\Enums\Countries;
use App\Enums\TimeZone;
use App\Enums\UserType;
use BenSampo\Enum\Rules\EnumValue;
use DateTimeZone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * @bodyParam firstName string required first name of the expert.
 * @bodyParam lastName string required first name of the expert.
 * @bodyParam email email required The email of the expert.
 * @bodyParam password string required the desired password.
 * @bodyParam image file The image.
 * @bodyParam job string required job of the expert.
 * @bodyParam country string required country of the expert.
 * @bodyParam time_zone string required country of the expert.
 * @bodyParam _method string This is a problem with PHP.Example: PUT
 * @package App\Http\Requests\Auth
 */
class StoreExpertRequest extends FormRequest
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
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:6', 'max:255'],
            'image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'job' => ['required', 'max:255'],
            'country' => ['required', Rule::in($countries)],
            'time_zone' => ['required', Rule::in($timeZones)],

        ];
    }
}
