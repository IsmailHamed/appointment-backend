<?php

namespace Database\Factories;

use App\Enums\Days;
use App\Models\Expert;
use App\Models\WorkHour;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkHourFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkHour::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'expert_id' => Expert::factory(),
            'day' => Days::getRandomValue(),
            'from' => $this->faker->time($format = 'H:i', $max = 'now'),
            'to' => $this->faker->time($format = 'H:i', $max = 'now'),
        ];
    }

}
