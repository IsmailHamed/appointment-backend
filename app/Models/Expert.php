<?php


namespace App\Models;


use App\Enums\ExpertStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Expert extends Model
{
    use HasFactory;

    protected $fillable = [
        'job',
        'country',
        'time_zone',
    ];
    protected $guarded = [
        'user_id',
        'status'
    ];
    protected $attributes = [
        'status' => ExpertStatus::PENDING,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workHours()
    {
        return $this->hasMany(WorkHour::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /*
     Collision if;
    start time of new appointment is between start and end of any record
    or, end time of new appointment is between start and end of any record
    or, start time of new appointment is before start time of a record
    AND end time of new appointment is after end time of any record
 $leave_exists = Leave::where('employee_id',$request->employee_id)
        ->whereBetween('from_date',[$request->from_date, $request->to_date])
        ->orWhereBetween('to_date',[$request->from_date, $request->to_date])
        ->orWhere(function($query) use($request){
            $query->where('from_date','<=',$request->from_date)
                ->where('to_date','>=',$request->to_date)
        })->first();
     */

    public function availability($from, $to)
    {
        $isAvailable = $this->bookings()
            ->Where(function ($query) use ($from, $to) {
                $query->where('start_at', '>', $from)->where('start_at', '<', $to)
                    ->orWhere('finish_at', '>', $from)->where('finish_at', '<', $to)
                    ->orwhere('start_at', '<=', $from)
                    ->where('finish_at', '>=', $to);
            })->exists();
        return !$isAvailable;
    }
}
