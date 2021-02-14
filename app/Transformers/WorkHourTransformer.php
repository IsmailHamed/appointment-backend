<?php


namespace App\Transformers;


use App\Models\WorkHour;

class WorkHourTransformer extends TransformerAbstract

{
    public function transform(WorkHour $workHour)
    {
        $time_zone = $this->getTimeZoneToUser();
        return [
            'identifier' => (int)$workHour->id,
            'day' => (int)$workHour->day,
            'openAt' => $this->convertTimeFromUTCToTimeZone($workHour->from, $time_zone),
            'closeAt' => $this->convertTimeFromUTCToTimeZone($workHour->to,$time_zone),
            'creationDate' => (string)$this->convertDateFromUTCToTimeZone($workHour->created_at, $time_zone),
            'lastChange' => (string)$this->convertDateFromUTCToTimeZone($workHour->updated_at, $time_zone),
            'deleteDate' => isset($workHour->deleted_at) ? (string)$this->convertDateFromUTCToTimeZone($workHour->deleted_at, $time_zone) : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attribute = [
            'identifier' => 'id',
            'day' => 'day',
            'openAt' => 'from',
            'closeAt' => 'to',
            'timeZone' => 'time_zone',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deleteDate' => 'deleted_at',
        ];
        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'identifier',
            'day' => 'day',
            'from' => 'openAt',
            'to' => 'closeAt',
            'time_zone' => 'timeZone',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deleteDate',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
