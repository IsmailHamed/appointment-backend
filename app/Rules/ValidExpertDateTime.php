<?php

namespace App\Rules;

use DateTime;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class ValidExpertDateTime implements Rule
{

    public function __construct()
    {
        //
    }

    public function passes($attribute, $from)
    {
        $isValidDate = $this->checkIsValidDate($from);
        if ($isValidDate) {
            $expert = request()->route()->expert;
            $duration = request('duration');
            $to = Carbon::createFromDate($from)->copy()
                ->addMinutes($duration)->toDateTimeString();
            return $expert->availability($from, $to);
        } else {
            return true;
        }
    }

    public function message()
    {
        return 'The expert is unavailable at selected time.find an alternate time.';
    }

    //Todo add this function to helper it's duplicate code
    function checkIsValidDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
