<?php

namespace App\Models;

use App\Traits\TimeHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class WorkHour extends Model
{
    use HasFactory, TimeHelper;

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

    public function setFromAttribute($from)
    {
        $route = request()->route();
        if ($route) {
            $expert = $route->expert;
            $time_zone_expert = $expert->time_zone;
            $from_UTC = Carbon::createFromFormat('H:i', $from, $time_zone_expert)
                ->setTimezone('UTC');
            $from = $from_UTC->format('H:i');
        }
        $this->attributes['from'] = $from;
    }

    public function setToAttribute($to)
    {
        $route = request()->route();
        if ($route) {
            $expert = $route->expert;
            $time_zone_expert = $expert->time_zone;
            $to_UTC = Carbon::createFromFormat('H:i', $to, $time_zone_expert)
                ->setTimezone('UTC');
            $to = $to_UTC->format('H:i');
        }
        $this->attributes['to'] = $to;
    }
}
