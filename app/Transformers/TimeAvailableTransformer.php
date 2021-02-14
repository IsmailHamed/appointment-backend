<?php


namespace App\Transformers;


use App\Models\WorkHour;

class TimeAvailableTransformer extends TransformerAbstract

{
    public function transform($timeAvailable)
    {
        $time_zone = $this->getTimeZoneToUser();
        $timeSlots = $timeAvailable->timeSlots;
        foreach ($timeSlots as $key => $timeSlot) {
            $timeSlot['from'] = $this->convertTimeFromUTCToTimeZone($timeSlot['from'], $time_zone);
            $timeSlot['to'] = (string)$this->convertTimeFromUTCToTimeZone($timeSlot['to'], $time_zone);
            $timeSlots[$key]=$timeSlot;
        }
        return [
            'duration' => (int)$timeAvailable->duration,
            'timeSlots' => $timeSlots,
        ];
    }

    public static function originalAttribute($index)
    {
        $attribute = [
            'duration' => 'duration',
            'timeSlots' => 'timeSlots',
        ];
        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'duration' => 'duration',
            'timeSlots' => 'timeSlots',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
