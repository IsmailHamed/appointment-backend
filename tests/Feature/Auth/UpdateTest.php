<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\Feature\APITestCase;

class UpdateTest extends APITestCase
{

    public function test_update_user_information_if_user_unauthorized()
    {
        $user = User::factory()->make();
        $data =
            [
                'firstName' => 'New first name',
                'lastName' => 'New last name',
                'password' => 'secret',
                'image' => UploadedFile::fake()->image('Newavatar.png'),
            ];
        $response = $this->putJson('auth/update', $data);
        $response
            ->assertStatus(401)
            ->assertJson(['message' => "Unauthorized", 'status_code' => 401]);
    }

    public function test_update_user_information()
    {
        $user = User::factory()->make();
        $data =
            [
                'firstName' => 'New first name',
                'lastName' => 'New last name',
                'password' => 'secret',
                'image' => UploadedFile::fake()->image('Newavatar.png'),
            ];
        $response = $this->actingAs($user)
            ->putJson('auth/update', $data);
        $response
            ->assertStatus(200)
            ->assertJson(['message' => "User's information updated successfully", 'status_code' => 200]);
        $user = $user->refresh();
        $this->assertEquals($user->first_name, 'New first name');
        $this->assertEquals($user->last_name, 'New last name');
//        Storage::disk('testing')->assertMissing(User::$ImagesDir . '/' . $oldImageName);
//        Storage::disk('testing')->assertExists(User::$ImagesDir . '/' . $user->image_name);
    }

    public function test_update_user_information_without_update_image()
    {
        $user = User::factory()->make();
        $response = $this->actingAs($user)
            ->putJson('auth/update', $this->data());
        $response
            ->assertStatus(200)
            ->assertJson(['message' => "User's information updated successfully", 'status_code' => 200]);
        $user = $user->refresh();
        $this->assertEquals($user->first_name, 'New first name');
        $this->assertEquals($user->last_name, 'New last name');
//        Storage::disk('testing')->assertExists(User::$ImagesDir . '/' . $user->image_name);
    }

    public function test_is_password_is_required()
    {
        $user = User::factory()->make();
        $response = $this->actingAs($user)
            ->putJson('auth/update', array_merge($this->data(), ['password' => '']));
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

    public function test_max_length_to_first_name()
    {
        $user = User::factory()->make();
        $firstName = str_repeat("firstName", 255);
        $response = $this->actingAs($user)
            ->putJson('auth/update', array_merge($this->data(), ['firstName' => $firstName]));
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
        $user = User::factory()->make();
        $lastName = str_repeat("lastName", 255);
        $response = $this->actingAs($user)
            ->putJson('auth/update', array_merge($this->data(), ['lastName' => $lastName]));
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
        $user = User::factory()->make();
        $password = $this->faker()->password(256, 257);
        $response = $this->actingAs($user)
            ->putJson('auth/update', array_merge($this->data(), ['password' => $password]));
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

    public function test_min_length_to_password()
    {
        $user = User::factory()->make();
        $response = $this->actingAs($user)
            ->putJson('auth/update', array_merge($this->data(), ['password' => 'p']));
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
        $user = User::factory()->make();
        $image = UploadedFile::fake()->create('avatar.png', 2050);
        $response = $this->actingAs($user)
            ->putJson('auth/update', array_merge($this->data(), ['image' => $image]));
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
        $user = User::factory()->make();
        $image = UploadedFile::fake()->create('avatar.pdf', 100, 'pdf');
        $response = $this->actingAs($user)
            ->putJson('auth/update', array_merge($this->data(), ['image' => $image]));
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

    public function data()
    {
        $data =
            [
                'firstName' => 'New first name',
                'lastName' => 'New last name',
                'password' => 'secret',
            ];
        return $data;
    }

}
