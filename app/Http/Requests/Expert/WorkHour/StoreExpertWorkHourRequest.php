<?php

namespace App\Http\Requests\Expert\WorkHour;

use App\Enums\Days;
use App\Traits\MaintenanceMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreExpertWorkHourRequest extends FormRequest
{
    //ToDo check permission and allow to expert store workHours
    public function authorize()
    {
        return Auth::user()->isAdmin();
    }

    public function rules()
    {
        return [
            '*.day' => [
                'required',
                Rule::in(Days::getValues())
            ],
            '*.from' => 'required|date_format:H:i',
            '*.to' => 'required|date_format:H:i',
        ];
    }

}
