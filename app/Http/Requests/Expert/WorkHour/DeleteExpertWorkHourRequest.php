<?php

namespace App\Http\Requests\Expert\WorkHour;

use App\Traits\MaintenanceMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteExpertWorkHourRequest extends FormRequest
{

    //ToDo check permission and allow to expert delete workHours
    public function authorize()
    {
        return Auth::user()->isAdmin();
    }

    public function rules()
    {
        return [];
    }

}
