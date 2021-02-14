<?php

namespace Database\Factories;

use App\Enums\TimeZone;
use App\Enums\ExpertStatus;
use App\Enums\UserType;
use App\Models\Expert;
use App\Models\User;
use App\Traits\FileManagement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class ExpertFactory extends Factory
{
    use FileManagement;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expert::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'user_id' => User::factory(['user_type' => UserType::EXPERT]),
            'job' => $this->faker->jobTitle,
            'country' => $this->faker->country,
            'time_zone' => $this->faker->timezone,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

}
