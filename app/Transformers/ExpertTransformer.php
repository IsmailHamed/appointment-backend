<?php

namespace App\Transformers;


use App\Models\Expert;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ExpertTransformer extends TransformerAbstract
{
    protected $availableIncludes =
        [
            'workHours',
        ];

    public function transform(Expert $expert)
    {
        $time_zone = $this->getTimeZoneToUser();

        return [
            'identifier' => (int)$expert->id,
            'firstName' => ucfirst($expert->user->first_name),
            'lastName' => ucfirst($expert->user->last_name),
            'email' => (string)$expert->user->email,
            'status' => (int)$expert->user->status,
            'imageLink' => isset($expert->user->image_name) ? Storage::url(User::$ImagesDir . '/' . $expert->user->image_name) : null,
            'emailVerified' => $expert->user->hasVerifiedEmail(),
            'job' => $expert->job,
            'country' => $expert->country,
            'timeZone' => $expert->time_zone,
            'creationDate' => (string)$this->convertDateFromUTCToTimeZone($expert->created_at, $time_zone),
            'lastChange' => (string)$this->convertDateFromUTCToTimeZone($expert->updated_at, $time_zone),
            'deleteDate' => isset($expert->deleted_at) ? (string)$this->convertDateFromUTCToTimeZone($expert->deleted_at, $time_zone) : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attribute = [
            'identifier' => 'id',
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'password_confirmation' => 'password_confirmation',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deleteDate' => 'deleted_at',
            'timeZone' => 'time_zone',
            'job' => 'job',
            'country' => 'country',
            'email' => 'email',
            'status' => 'status',
            'password' => 'password',
            'image' => 'image',
            'code' => 'code',
            'token' => 'token',
            'date' => 'date',
        ];
        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'identifier',
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'password_confirmation' => 'password_confirmation',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deletedDate',
            'time_zone' => 'timeZone',
            'job' => 'job',
            'country' => 'country',
            'email' => 'email',
            'status' => 'status',
            'password' => 'password',
            'image' => 'image',
            'code' => 'code',
            'token' => 'token',
            'date' => 'date',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public function includeWorkHours(Expert $expert)
    {
        $workHours = $expert->workHours->sortBy('day');
        return $this->collection($workHours, new WorkHourTransformer());
    }

}
