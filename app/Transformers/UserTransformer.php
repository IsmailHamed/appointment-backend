<?php

namespace App\Transformers;


use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserTransformer extends TransformerAbstract
{

    public function transform(User $user)
    {
        $time_zone = $this->getTimeZoneToUser();

        return [
            'identifier' => (int)$user->id,
            'firstName' => ucfirst($user->first_name),
            'lastName' => ucfirst($user->last_name),
            'email' => (string)$user->email,
            'status' => (int)$user->status,
            'imageLink' => isset($user->image_name) ? Storage::url(User::$ImagesDir . '/' . $user->image_name) : null,
            'emailVerified' => $user->hasVerifiedEmail(),
            'creationDate' => (string)$this->convertDateFromUTCToTimeZone($user->created_at, $time_zone),
            'lastChange' => (string)$this->convertDateFromUTCToTimeZone($user->updated_at, $time_zone),
            'deleteDate' => isset($user->deleted_at) ? (string)$this->convertDateFromUTCToTimeZone($user->deleted_at, $time_zone) : null,
        ];
    }

    public static function originalAttribute($index)
    {
        $attribute = [
            'identifier' => 'id',
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'password_confirmation' => 'password_confirmation',
            'userId' => 'user_id',
            'email' => 'email',
            'status' => 'status',
            'password' => 'password',
            'image' => 'image',
            'code' => 'code',
            'token' => 'token',
            'uuid' => 'uuid',
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
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'password_confirmation' => 'password_confirmation',
            'user_id' => 'userId',
            'email' => 'email',
            'status' => 'status',
            'password' => 'password',
            'image' => 'image',
            'code' => 'code',
            'uuid' => 'uuid',
            'token' => 'token',
            'time_zone' => 'timeZone',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deletedDate',
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
