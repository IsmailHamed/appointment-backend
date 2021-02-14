<?php

namespace App\Transformers;


use App\Models\Booking;

class BookingTransformer extends TransformerAbstract
{

    public function transform(Booking $booking)
    {
        $time_zone = $this->getTimeZoneToUser();
        return [
            'identifier' => (int)$booking->id,
            'userIdentifier' => (int)$booking->user_id,
            'duration' => (int)$booking->duration,
            'startAt' => (string)$this->convertDateFromUTCToTimeZone($booking->start_at,$time_zone),
            'finishAt' => (string)$this->convertDateFromUTCToTimeZone($booking->finish_at,$time_zone),
            'status' => (int)$booking->status,
            'creationDate' => (string)$this->convertDateFromUTCToTimeZone($booking->created_at, $time_zone),
            'lastChange' => (string)$this->convertDateFromUTCToTimeZone($booking->updated_at, $time_zone),
            'deleteDate' => isset($booking->deleted_at) ? (string)$this->convertDateFromUTCToTimeZone($booking->deleted_at, $time_zone) : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attribute = [
            'identifier' => 'id',
            'userIdentifier' => 'user_id',
            'status' => 'status',
            'duration' => 'duration',
            'startAt' => 'start_at',
            'finishAt' => 'finish_at',
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
            'user_id' => 'userIdentifier',
            'status' => 'status',
            'duration' => 'duration',
            'start_at' => 'startAt',
            'finish_at' => 'finishAt',
            'time_zone' => 'timeZone',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deleteDate',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
