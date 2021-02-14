<?php


namespace App\Traits;

use DateTime;
use Illuminate\Support\Carbon;

trait TimeHelper
{
    protected function convertDateFromUTCToTimeZone($date, $time_zone)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $date, "UTC");
        return $date->setTimezone($time_zone)->toDateTimeString();
    }

    protected function convertDateFromTimeZoneToUTC($date, $time_zone)
    {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $date, $time_zone);
        return $date->setTimezone('UTC')->toDateTimeString();
    }

    protected function convertTimeFromUTCToTimeZone($date, $time_zone)
    {
        $date = Carbon::createFromFormat('H:i:s', $date, 'UTC');
        return $date->setTimezone($time_zone)->format('h:i A');
    }

    protected function convertTimeFromTimeZoneToUTC($date, $time_zone)
    {
        $date = Carbon::createFromFormat('H:i:s', $date, $time_zone);
        return $date->setTimezone('UTC')->format('h:i A');
    }

    function checkIsValidDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    protected function getTimeZoneToUser()
    {
        $time_zone = request('time_zone');
        if (is_null($time_zone)) {
            $ip = request()->ip();
            $time_zone = geoip($ip)->timezone;
        }
        return $time_zone;
    }
}
