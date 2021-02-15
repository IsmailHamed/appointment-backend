<?php


namespace Tests\Feature\Expert\WorkHour;


use App\Enums\Days;
use App\Models\Expert;
use App\Models\User;
use App\Models\WorkHour;
use Illuminate\Support\Carbon;
use Tests\Feature\APITestCase;

class StoreTest extends APITestCase
{
    public function test_day_is_required_when_store_work_hours()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $response = $this->actingAs($admin)
            ->postJson('experts/' . $expert->id . '/workHours',
                ['saturday' =>
                    [
                        'from' => $this->faker->time($format = 'H:i'),
                        'to' => $this->faker->time($format = 'H:i'),
                    ]
                ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "saturday.day" => ["The saturday.day field is required."],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_from_is_required_when_store_work_hours()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $response = $this->actingAs($admin)
            ->postJson('experts/' . $expert->id . '/workHours',
                ['saturday' =>
                    [
                        'day' => Days::getRandomValue(),
                        'to' => $this->faker->time($format = 'H:i'),
                    ]
                ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "saturday.from" => ["The saturday.from field is required."],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_to_is_required_when_store_work_hours()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $response = $this->actingAs($admin)
            ->postJson('experts/' . $expert->id . '/workHours',
                ['saturday' =>
                    [
                        'day' => Days::getRandomValue(),
                        'from' => $this->faker->time($format = 'H:i'),
                    ]
                ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "saturday.to" => ["The saturday.to field is required."],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_store_work_hours_by_invalid_value_to_day()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $response = $this->actingAs($admin)
            ->postJson('experts/' . $expert->id . '/workHours',
                ['saturday' =>
                    [
                        'day' => "50",
                        'from' => $this->faker->time($format = 'H:i'),
                        'to' => $this->faker->time($format = 'H:i'),
                    ]
                ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "saturday.day" => ["The selected saturday.day is invalid."],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_store_work_hours_to_expert_by_user_not_authorized()
    {
        $expert = Expert::factory()->create();
        $data = $this->requestData();
        $response = $this->postJson('experts/' . $expert->id . '/workHours', $data);
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    public function test_store_work_hours_to_expert_by_user_not_administrator()
    {
        $expert = Expert::factory()->create();
        $user = User::factory()->create();
        $data = $this->requestData();
        $response = $this->actingAs($user)
            ->postJson('experts/' . $expert->id . '/workHours', $data);
        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.', 'status_code' => 403]);
    }

    public function test_store_work_hours_to_expert()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $data = $this->requestData();
        $response = $this->actingAs($admin)
            ->postJson('experts/' . $expert->id . '/workHours', $data);
        $response->assertStatus(200);
        $this->assertEquals(7, $expert->workHours->count());
        $response->assertJson(['message' => "The expert's work hours added successfully.", 'status_code' => 200]);
    }

    public function test_store_work_hours_to_expert_by_UTC()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create([
            'time_zone' => 'EET'
        ]);
        $data = [
            'saturday' => [
                'day' => Days::ALL,
                'from' => '13:00',
                'to' => '14:00'
            ]
        ];
        $response = $this->actingAs($admin)
            ->postJson('experts/' . $expert->id . '/workHours', $data);
        $response->assertStatus(200);
        $workHours = $expert->workHours->first();
        $from = $data['saturday']['from'];
        $time_zone_expert = $expert->time_zone;
        $to = $data['saturday']['to'];
        $this->assertNotEquals((string)$workHours->from, $from . ':00');
        $this->assertNotEquals((string)$workHours->to, $to . ':00');
        $from_UTC = Carbon::createFromFormat('H:i', $from,$time_zone_expert)
            ->setTimezone('UTC')->format('H:i:s');
        $to_UTC = Carbon::createFromFormat('H:i', $to, $time_zone_expert)
            ->setTimezone('UTC')->format('H:i:s');
        $this->assertEquals((string)$workHours->from, (string)$from_UTC);
        $this->assertEquals((string)$workHours->to, (string)$to_UTC);
    }

    private function requestData()
    {
        $data = [
            'day' => Days::getRandomValue(),
            'from' => $this->faker->time($format = 'H:i'),
            'to' => $this->faker->time($format = 'H:i')
        ];
        return [
            'saturday' => $data,
            'sunday' => $data,
            'monday' => $data,
            'tuesday' => $data,
            'wednesday' => $data,
            'thursday' => $data,
            'friday' => $data,
        ];
    }

}
