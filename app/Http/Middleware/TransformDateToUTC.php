<?php

namespace App\Http\Middleware;

use App\Traits\TimeHelper;
use Closure;
use Illuminate\Http\Request;

class TransformDateToUTC
{
    use TimeHelper;

    public function handle(Request $request, Closure $next)
    {
        $start_at = $request->get('start_at');
        $isValidDate = $this->checkIsValidDate($start_at);
        if ($isValidDate) {
            $request_Input = $request->all();
            $time_zone = $this->getTimeZoneToUser();
            $start_at_UTC = $this->convertDateFromTimeZoneToUTC($start_at, $time_zone);
            $request_Input['start_at'] = $start_at_UTC;
            $request->replace($request_Input);
        }
        return $next($request);
    }

}
