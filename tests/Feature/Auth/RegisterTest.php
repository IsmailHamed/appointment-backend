<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\Feature\APITestCase;

class RegisterTest extends APITestCase
{

    public function test_first_name_is_required()
    {
        $response = $this->postJson('auth/register', array_merge($this->data(), ['firstName' => '']));
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
        $response = $this->postJson('auth/register', array_merge($this->data(), ['lastName' => '']));
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
        $response = $this->postJson('auth/register', array_merge($this->data(), ['email' => '']));;
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

    public function test_max_length_to_first_name()
    {
        $firstName = str_repeat("firstName", 255);
        $response = $this->postJson('auth/register', array_merge($this->data(), ['firstName' => $firstName]));
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
        $lastName = str_repeat("lastName", 255);
        $response = $this->postJson('auth/register', array_merge($this->data(), ['lastName' => $lastName]));
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
        $email = str_repeat("email", 255);
        $response = $this->postJson('auth/register', array_merge($this->data(), ['email' => $email . '@localhost.com']));;
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
        $response = $this->postJson('auth/register', array_merge($this->data(), ['password' => '']));
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
        $password = $this->faker()->password(256, 257);
        $response = $this->postJson('auth/register', array_merge($this->data(), ['password' => $password]));
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
        $response = $this->postJson('auth/register', array_merge($this->data(), ['password' => 'p']));
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
        $response = $this->postJson('auth/register', array_merge($this->data(), ['email' => 'ismail.com']));
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
        $user = User::factory()->create([
            'email' => 'ismail@localhost.com',
        ]);
        $response = $this->postJson('auth/register', $this->data());
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
        $data = $this->data();
        unset($data['image']);

        $response = $this->postJson('auth/register', $data);
        $response->assertStatus(200);
    }

    public function test_max_image_size_is_2048()
    {
        $image = UploadedFile::fake()->create('avatar.png', 2050);
        $response = $this->post('auth/register', array_merge($this->data(), ['image' => $image]));
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
        $image = UploadedFile::fake()->create('avatar.pdf', 100, 'pdf');
        $response = $this->postJson('auth/register', array_merge($this->data(), ['image' => $image]));
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

    public function test_register_user()
    {
        $response = $this->postJson('auth/register', $this->data());
        $response->assertStatus(200);
        $users = User::all();
        $this->assertEquals(1, $users->count());
//        Storage::disk('testing')->assertExists(User::$ImagesDir . '/' . $users->first()->image_name);
        $response->assertJsonStructure([
            'me',
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function test_register_user_without_image()
    {
        $data = $this->data();
        unset($data['image']);

        $response = $this->postJson('auth/register', $data);
        $response->assertStatus(200);
        $users = User::all();
        $this->assertEquals(1, $users->count());
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }


    private function data()
    {
        return [
            'firstName' => 'first name',
            'lastName' => 'last name',
            'email' => 'ismail@localhost.com',
            'password' => 'secret',
            'image' => UploadedFile::fake()->image('avatar.png'),
        ];
    }

}
