<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration',
        'start_at',
        'finish_at',
    ];
    protected $guarded = [
        'user_id',
        'status'
    ];
    protected $attributes = [
        'status' => BookingStatus::PENDING,
    ];
    protected $casts = [
        'date' => 'datetime',
    ];
    protected $dates = [
        'start_at',
        'finish_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

}
