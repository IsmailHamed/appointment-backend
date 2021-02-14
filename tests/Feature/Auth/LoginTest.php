<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Models\Expert;
use App\Models\User;
use App\Transformers\ExpertTransformer;
use App\Transformers\UserTransformer;
use Carbon\Carbonite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\Feature\APITestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginTest extends APITestCase
{

    public function test_email_is_required()
    {
        $response = $this->postJson('auth/login', array_merge($this->requestData(), ['email' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "email" => ['The email field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_password_is_required()
    {
        $response = $this->postJson('auth/login', array_merge($this->requestData(), ['password' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "password" => ['The password field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_login_as_user()
    {
        $userData = $this->requestData();
        $user = User::factory()->create($userData);
        $response = $this->postJson('auth/login', $userData);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
            'me',
        ]);
        $dataResponse = json_decode($response->getContent(), true);
        $this->assertEqualsCanonicalizing($dataResponse['me'], $this->dataResponse($user));
    }

    public function test_login_as_expert()
    {
        $userData = array_merge($this->requestData(), ['user_type' => UserType::EXPERT]);
        $user = User::factory()->create($userData);
        Expert::factory()->create(['user_id'=>$user->id]);
        $response = $this->postJson('auth/login', $userData);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
            'me',
        ]);
        $dataResponse = json_decode($response->getContent(), true);
        $this->assertEqualsCanonicalizing($dataResponse['me'], $this->dataResponse($user));
    }

    public function test_login_user_if_not_registered()
    {
        $response = $this->postJson('auth/login', $this->requestData());
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    public function test_refresh_a_token()
    {
        $user = User::factory()->create(['user_type'=>UserType::USER]);
        $token = JWTAuth::fromUser($user);
        $ttl = JWTAuth::factory()->getTTL(); // minutes
        Carbonite::jumpTo(now()->addMinutes($ttl + 1)); // Jump to a given moment
        $response = $this->getJson('auth/refresh?token=' . $token);
        $response->assertStatus(200);
        $response->assertJsonMissing(['access_token', $token]);
    }

    public function test_refresh_a_token_after_refreshed_time_finished()
    {
        $user = User::factory()->make();
        $refresh_ttl = Config::get('jwt.refresh_ttl');
        $token = JWTAuth::fromUser($user);
        Carbonite::jumpTo(now()->addMinutes($refresh_ttl + 1)); // Jump to a given moment
        $response = $this->getJson('auth/refresh?token=' . $token);
        $response->assertStatus(401)
            ->assertJson(['message' => 'Token has expired and can no longer be refreshed', 'status_code' => 401]);
    }

    public function test_logout_user()
    {
        $user = User::factory()->make();
        $token = JWTAuth::fromUser($user);
        $response = $this->actingAs($user)
            ->get('auth/logout?token=' . $token);
        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out', 'status_code' => 200]);
    }



    private function dataResponse($user)
    {
        if ($user->isExpert()) {
            $userInformation = fractal($user->expert, new ExpertTransformer())->toArray();

        } else {
            $userInformation = fractal($user, new UserTransformer())->toArray();
        }
        return $userInformation['data'];
    }
    private function requestData()
    {
        return [
            'email' => $this->faker()->email,
            'password' => $this->faker()->password,
        ];
    }}
