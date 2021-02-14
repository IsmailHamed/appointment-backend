<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'from',
        'to'
    ];
    protected $guarded = [
        'expert_id',
    ];

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }
}
