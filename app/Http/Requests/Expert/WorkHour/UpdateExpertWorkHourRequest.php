<?php


namespace App\Http\Requests\Expert\WorkHour;


use App\Enums\Days;
use App\Traits\MaintenanceMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateExpertWorkHourRequest extends FormRequest
{
    //ToDo check permission and allow to expert update workHours
    public function authorize()
    {
        return Auth::user()->isAdmin();
    }
    public function rules()
    {
        return [
            'day' => Rule::in(Days::getValues()),
            'from' => 'date_format:H:i',
            'to' => 'date_format:H:i',
        ];
    }


}
