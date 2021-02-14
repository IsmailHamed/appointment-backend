<?php

namespace Tests\Unit\Auth;

use App\Enums\ExpertStatus;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_email_is_unique()
    {
        $this->expectExceptionCode(23000);
        $user = User::factory()->create([
            'email' => 'ismail@ismail.com',
        ]);
        $user = User::factory()->create([
            'email' => 'ismail@ismail.com',
        ]);
    }

    public function test_default_status_to_user_is_registered()
    {
        $user = User::factory()->create();
        $this->assertSame($user->status, UserStatus::REGISTERED);
    }

    public function test_default_email_verified_at_is_null()
    {
        $user = User::create($this->data());
        $this->assertSame($user->email_verified_at, null);
    }

    public function test_if_password_hashed()
    {
        $user = User::factory()->create(['password' => 'secret']);
        $this->assertTrue(Hash::check('secret', $user->password));
    }

    private function data()
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'test@localhost.com',
            'password' => 'secret',
        ];
    }
}
