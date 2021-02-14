<?php

namespace Tests\Feature\Auth;

use App\Mail\OTP;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\APITestCase;

class ForgetPasswordTest extends APITestCase
{

    public function test_request_forget_password()
    {
        Mail::fake();
        Mail::assertNothingSent();

        $user = User::factory()->create();
        $response = $this->postJson('auth/request-forget-password', ['email' => $user->email]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            "message" => "Code is sent successfully",
            "status_code" => 200
        ]);
        Mail::assertSent(OTP::class, 1);
    }

    public function test_request_forget_password_when_no_email()
    {
        User::factory()->create();
        $response = $this->postJson('auth/request-forget-password');
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "email" => ['The email field is required.'],
                ],
                "status_code" => 422
            ]);
    }

    public function test_request_forget_password_when_email_not_registered()
    {
        $response = $this
            ->postJson('auth/request-forget-password', ['email' => $this->faker->email]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "email" => ['The selected email is invalid.'],
                ],
                "status_code" => 422
            ]);
    }

    public function test_validate_forget_password()
    {
        $user = User::factory()->create();
        $key = $user->email . config('app.key');
        $code = \Tzsk\Otp\Facades\Otp::generate($key);
        $response = $this
            ->postJson('auth/forget-password',
                [
                    'email' => $user->email,
                    'code' => $code,
                    'password' => "secret",
                ]
            );
        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Password is updated successfully',
                'status_code' => 200,
            ]);
    }

    public function test_validate_forget_password_when_no_email()
    {
        User::factory()->create();
        $response = $this->postJson('auth/forget-password');
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "email" => ['The email field is required.'],
                ],
                "status_code" => 422
            ]);
    }

    public function test_validate_forget_password_when_no_password()
    {
        User::factory()->create();
        $response = $this->postJson('auth/forget-password');
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "password" => ['The password field is required.'],
                ],
                "status_code" => 422
            ]);
    }

    public function test_validate_forget_password_when_email_not_registered()
    {
        $response = $this
            ->postJson('auth/forget-password', ['email' => $this->faker->email]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "email" => ['The selected email is invalid.'],
                ],
                "status_code" => 422
            ]);
    }

}
