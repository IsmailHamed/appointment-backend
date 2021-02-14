<?php

namespace App\Http\Requests\Expert;

use App\Rules\ValidExpertDateTime;
use Illuminate\Foundation\Http\FormRequest;

class GetAvailabilityTimeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
        ];
    }
}
