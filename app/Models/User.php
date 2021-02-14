<?php

namespace App\Models;

use App\Enums\ExpertStatus;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;

    static $ImagesDir = 'UsersImages';


    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'image_name',
    ];
    protected $guarded = [
        'status',
        'user_type'
    ];
    protected $hidden = [
        'remember_token',
        'password'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $attributes = [
        'status' => ExpertStatus::PENDING,
    ];
    protected $guard_name = 'api';

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function expert()
    {
        return $this->hasOne(Expert::class);
    }
    //todo add scop
    public function isAdmin()
    {
        return $this->user_type == UserType::ADMINISTRATOR;
    }

    public function isExpert()
    {
        return $this->user_type == UserType::EXPERT;
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }
}
