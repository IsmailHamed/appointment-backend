<?php


namespace App\Traits;


use Auth;

trait MaintenanceMode
{
    protected function rootAccess()
    {
        $allow = false;
        $userAuthentication = Auth::user();
        $isDebugMode = config('app.debug');
        if (!is_null($userAuthentication) && $isDebugMode) {
            $userEmail = $userAuthentication->email;
            if (($userEmail == 'super-admin@bookmyflight.com')) {
                $allow = true;
            }
        }
        return $allow;
    }
}
