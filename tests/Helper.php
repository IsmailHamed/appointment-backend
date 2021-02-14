<?php


namespace Tests;


use App\Enums\UserType;
use App\Models\User;

trait Helper
{
    protected function getAdministratorUser()
    {
        $user = User::factory()->create([
            'user_type' => UserType::ADMINISTRATOR
        ]);
        return $user;
    }

}
