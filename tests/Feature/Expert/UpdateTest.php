<?php

namespace Tests\Feature\Expert;

use App\Models\Expert;
use App\Models\User;
use Carbon\Carbonite;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Tests\Feature\APITestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTest extends APITestCase
{

    public function test_update_expert_information_if_user_unauthorized()
    {
        $expert = Expert::factory()->create();
        $response = $this->putJson('experts/' . $expert->id, $this->data());
        $response
            ->assertStatus(401)
            ->assertJson(['message' => "Unauthorized", 'status_code' => 401]);
    }

    public function test_update_expert_information_by_user_not_administrator()
    {
        $user = User::factory()->create();
        $expert = Expert::factory()->create();
        $response = $this->actingAs($user)
            ->putJson('experts/' . $expert->id, $this->data());
        $response
            ->assertStatus(403)
            ->assertJson(['message' => "This action is unauthorized.", 'status_code' => 403]);
    }

    public function test_update_expert_information()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $data = $this->data();
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, $data);
        $response
            ->assertStatus(200)
            ->assertJson(['message' => "Expert's information updated successfully", 'status_code' => 200]);
        $expert = $expert->refresh();
        $this->assertEquals($expert->user->first_name, $data['firstName']);
        $this->assertEquals($expert->user->last_name, $data['lastName']);
        $this->assertEquals($expert->job, $data['job']);
        $this->assertEquals($expert->country, $data['country']);
        $this->assertEquals($expert->time_zone, $data['timeZone']);
//        Storage::disk('testing')->assertMissing(User::$ImagesDir . '/' . $oldImageName);
//        Storage::disk('testing')->assertExists(User::$ImagesDir . '/' . $expert->image_name);
    }

    public function test_update_expert_information_without_update_image()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $data = $this->data();
        unset($data['image']);
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, $data);
        $response
            ->assertStatus(200)
            ->assertJson(['message' => "Expert's information updated successfully", 'status_code' => 200]);
        $expert = $expert->refresh();
        $this->assertEquals($expert->user->first_name, $data['firstName']);
        $this->assertEquals($expert->user->last_name, $data['lastName']);
        $this->assertEquals($expert->job, $data['job']);
        $this->assertEquals($expert->country, $data['country']);
        $this->assertEquals($expert->time_zone, $data['timeZone']);
//        Storage::disk('testing')->assertExists(Expert::$ImagesDir . '/' . $expert->image_name);
    }

    public function test_max_length_to_first_name()
    {
        $admin = $this->getAdministratorUser();

        $expert = Expert::factory()->create();
        $firstName = str_repeat("firstName", 255);
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['firstName' => $firstName]));
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

        $expert = Expert::factory()->create();
        $lastName = str_repeat("lastName", 255);
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['lastName' => $lastName]));
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

    public function test_max_length_to_password()
    {
        $admin = $this->getAdministratorUser();

        $expert = Expert::factory()->create();
        $password = $this->faker()->password(256, 257);
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['password' => $password]));
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

        $expert = Expert::factory()->create();
        $job = str_repeat("job", 255);
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['job' => $job]));
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

        $expert = Expert::factory()->create();
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['password' => 'p']));
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

    public function test_max_image_size_is_2048()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $image = UploadedFile::fake()->create('avatar.png', 2050);
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['image' => $image]));
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
        $expert = Expert::factory()->create();
        $image = UploadedFile::fake()->create('avatar.pdf', 100, 'pdf');
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['image' => $image]));
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

    public function test_invalid_time_zone()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['timeZone' => 'timeZone']));;
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
        $expert = Expert::factory()->create();

        $response = $this->actingAs($admin)
            ->putJson('experts/' . $expert->id, array_merge($this->data(), ['country' => 'country']));;
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

    public function data()
    {
        $data =
            [
                'firstName' => $this->faker->firstName,
                'lastName' => $this->faker->lastName,
                'password' => $this->faker->password,
                'job' => $this->faker->jobTitle,
                'country' => $this->faker->country,
                'timeZone' => $this->faker->timezone,
                'image' => UploadedFile::fake()->image('Newavatar.png'),
            ];
        return $data;
    }

}
