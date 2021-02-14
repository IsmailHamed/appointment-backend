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

class LoginAsGuestTest extends APITestCase
{

    public function test_uuid_is_required()
    {
        $response = $this->postJson('auth/login-as-guest', array_merge($this->requestData(), ['uuid' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "uuid" => ['The uuid field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_first_name_is_required()
    {

        $response = $this->postJson('auth/login-as-guest', array_merge($this->requestData(), ['firstName' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "firstName" => ['The first name field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_login_as_guest()
    {
        $response = $this->postJson('auth/login-as-guest', $this->requestData());
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
            'me',
        ]);
        $users = User::all();
        $this->assertEquals(1, $users->count());
        $dataResponse = json_decode($response->getContent(), true);
        $this->assertEqualsCanonicalizing($dataResponse['me'], $this->dataResponse($users->first()));
    }

    private function dataResponse($user)
    {
        $userInformation = fractal($user, new UserTransformer())->toArray();
        return $userInformation['data'];
    }

    private function requestData()
    {
        return [
            'uuid' => $this->faker()->uuid,
            'firstName' => $this->faker()->firstName,
        ];
    }
}
