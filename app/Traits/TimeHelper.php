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
            $ip = $this->getIp();
//            $ip = request()->getClientIp();
            $time_zone = geoip($ip)->timezone;
        }
        return $time_zone;
    }

    public function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return server ip when no client ip found
    }
}
