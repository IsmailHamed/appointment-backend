<?php

namespace Database\Factories;

use App\Enums\ExpertStatus;
use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\User;
use App\Traits\FileManagement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class UserFactory extends Factory
{
    use FileManagement;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $image = UploadedFile::fake()->image('avatar.png');
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'image_name' => $this->upload_image($image, User::$ImagesDir),
            'first_name' => $this->faker->name,
            'last_name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'secret',
            'status' => UserStatus::REGISTERED,
            'user_type' => UserType::USER,
            'email_verified_at' => $this->faker->dateTime(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }

}
