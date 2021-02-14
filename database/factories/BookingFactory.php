<?php

namespace Database\Factories;

use App\Enums\BookingDuration;
use App\Enums\BookingStatus;
use App\Models\booking;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $duration = $this->faker->randomElement(BookingDuration::getValues());
        $startAt = Carbon::createFromTimestamp($this->faker->dateTime()->getTimestamp());
        $finishAt = $startAt->copy()->addMinutes($duration);
        return [
            'id' => $this->faker->unique()->randomNumber(),
            'user_id' => User::factory(),
            'expert_id' => Expert::factory(),
            'status' => $this->faker->randomElement(BookingStatus::getValues()),
            'duration' => $duration,
            'start_at' => $startAt->toDateTime(),
            'finish_at' => $finishAt->toDateTime(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
