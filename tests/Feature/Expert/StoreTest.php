<?php

namespace Tests\Feature\Expert;

use App\Models\Expert;
use App\Models\User;
use Carbon\Carbonite;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Tests\Feature\APITestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreTest extends APITestCase
{

    public function test_first_name_is_required()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['firstName' => '']));
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

    public function test_last_name_is_required()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['lastName' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "lastName" => ['The last name field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_email_is_required()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['email' => '']));;
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

    public function test_job_is_required()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['job' => '']));;
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "job" => ['The job field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_country_is_required()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['country' => '']));;
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "country" => ['The country field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_time_zone_is_required()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['timeZone' => '']));;
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "timeZone" => ['The time zone field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_invalid_time_zone()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['timeZone' => 'timeZone']));;
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "timeZone" => ['The selected time zone is invalid.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_invalid_country()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['country' => 'country']));;
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "country" => ['The selected country is invalid.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_max_length_to_first_name()
    {
        $admin = $this->getAdministratorUser();
        $firstName = str_repeat("firstName", 255);
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['firstName' => $firstName]));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "firstName" => ['The first name may not be greater than 255 characters.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_max_length_to_last_name()
    {
        $admin = $this->getAdministratorUser();
        $lastName = str_repeat("lastName", 255);
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['lastName' => $lastName]));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "lastName" => ['The last name may not be greater than 255 characters.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_max_length_to_email()
    {
        $admin = $this->getAdministratorUser();
        $email = str_repeat("email", 255);
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['email' => $email . '@localhost.com']));;
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "email" => ['The email may not be greater than 255 characters.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_is_password_is_required()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['password' => '']));
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

    public function test_max_length_to_password()
    {
        $admin = $this->getAdministratorUser();
        $password = $this->faker()->password(256, 257);
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['password' => $password]));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "password" => ['The password may not be greater than 255 characters.'],
                ],
                "status_code" => '422'
            ]);
    }
    public function test_max_length_to_job()
    {
        $admin = $this->getAdministratorUser();
        $job = str_repeat("job", 255);
        $response = $this->actingAs($admin)
            ->postJson('experts' , array_merge($this->requestData(), ['job' => $job]));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "job" => ['The job may not be greater than 255 characters.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_min_length_to_password()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['password' => 'p']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "password" => ['The password must be at least 6 characters.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_email_regex()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['email' => 'ismail.com']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "email" => ['The email must be a valid email address.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_is_email_unique()
    {
        $admin = $this->getAdministratorUser();
        $user = User::factory()->create([
            'email' => 'ismail@localhost.com',
        ]);
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['email' => 'ismail@localhost.com']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "email" => ['The email has already been taken.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_is_image_not_required()
    {
        $admin = $this->getAdministratorUser();
        $data = $this->requestData();
        unset($data['image']);
        $response = $this->actingAs($admin)
            ->postJson('experts', $data);
        $response->assertStatus(200);
    }

    public function test_max_image_size_is_2048()
    {
        $admin = $this->getAdministratorUser();
        $image = UploadedFile::fake()->create('avatar.png', 2050);
        $response = $this->actingAs($admin)->postJson('experts', array_merge($this->requestData(), ['image' => $image]));
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "image" => ['The image may not be greater than 2048 kilobytes.'],
                ],
                "status_code" => 422
            ]);
    }

    public function test_upload_invalid_type_image()
    {
        $admin = $this->getAdministratorUser();
        $image = UploadedFile::fake()->create('avatar.pdf', 100, 'pdf');
        $response = $this->actingAs($admin)
            ->postJson('experts', array_merge($this->requestData(), ['image' => $image]));
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "image" => [
                        "The image must be an image.",
                        "The image must be a file of type: jpeg, png, jpg, gif, svg."
                    ],
                ],
                "status_code" => 422
            ]);
    }

    public function test_store_expert_if_user_unauthorized()
    {
        $response = $this->postJson('experts', $this->requestData());
        $response
            ->assertStatus(401)
            ->assertJson(['message' => "Unauthorized", 'status_code' => 401]);
    }

    public function test_store_expert_by_user_not_administrator()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('experts', $this->requestData());
        $response
            ->assertStatus(403)
            ->assertJson(['message' => "This action is unauthorized.", 'status_code' => 403]);
    }

    public function test_store_expert()
    {
        $admin = $this->getAdministratorUser();
        $response = $this->actingAs($admin)
            ->postJson('experts', $this->requestData());
        $response->assertStatus(200);
        $experts = Expert::all();
        $this->assertEquals(1, $experts->count());
//        Storage::disk('testing')->assertExists(User::$ImagesDir . '/' . $users->first()->image_name);
        $response->assertJson(['message' => "The expert added successfully.", 'status_code' => 200]);

    }

    private function requestData()
    {
        return [
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'image' => UploadedFile::fake()->image('avatar.png'),
            'job' => $this->faker->jobTitle,
            'country' => $this->faker->country,
            'timeZone' => $this->faker->timezone,
        ];
    }


}
