<?php

namespace Database\Seeders;

use App\Enums\Days;
use App\Enums\ExpertStatus;
use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\Expert;
use App\Models\User;
use App\Models\WorkHour;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class ExpertsSeeder extends Seeder
{
    public function run()
    {
        $this->createExpert('William ', 'Jordan', 'Doctor',
            'Anabar', 'NZST', '06:00', '17:00');
        $this->createExpert('Quasi', 'Shawa', 'Civil engineer',
            'Syria', 'EEST', '06:00', '12:00');
        $this->createExpert('Shimaa', 'Badawy', 'Computer Engineer',
            'Egypt', 'EET', '13:00', '14:00');
    }

    private function createExpert($fitstName, $lastName, $job, $country, $time_zone, $from, $to)
    {
        $faker = \Faker\Factory::create();
        $user_id = $faker->unique()->randomNumber();
        $user_data = [
            'id' => $user_id,
            "first_name" => $fitstName,
            "last_name" => $lastName,
            "email" => $faker->email,
            'password' => bcrypt('secret'),
            'status' => UserStatus::ACTIVATED,
            'user_type' => UserType::EXPERT,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        User::insert($user_data);
        $expert_id = $faker->unique()->randomNumber();
        $expert_data = [
            'id' => $expert_id,
            'user_id' => $user_id,
            'status' => ExpertStatus::ACTIVATED,
            'job' => $job,
            'country' => $country,
            'time_zone' => $time_zone,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        Expert::insert($expert_data);
        $work_Hours =
            [
                'expert_id' => $expert_id,
                'day' => Days::ALL,
                'from' => $from,
                'to' => $to,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        WorkHour::insert($work_Hours);

    }

}
