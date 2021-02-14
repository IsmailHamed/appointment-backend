<?php

namespace App\Http\Requests\Expert\Booking;

use App\Enums\BookingDuration;
use App\Rules\ValidExpertDateTime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpertBookingRequest extends FormRequest
{
    //todo check if expert

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
        //todo Uuid to user
        return [
            'duration' => ['required', Rule::in(BookingDuration::getValues())],
            'start_at' => [
                'required',
                'date',
                'date_format:Y-m-d H:i:s',
                'after:' . date('Y-m-d H:i'),
                new ValidExpertDateTime()
            ],
        ];

    }
}
