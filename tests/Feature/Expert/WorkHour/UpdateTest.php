<?php


namespace Tests\Feature\Expert\WorkHour;


use App\Enums\Days;
use App\Models\Expert;
use App\Models\User;
use App\Models\WorkHour;
use Tests\Feature\APITestCase;

class UpdateTest extends APITestCase
{
    public function test_update_work_hours_to_expert_by_user_not_authorized()
    {
        $workHour = WorkHour::factory()->create();
        $response = $this->putJson('experts/' . $workHour->expert_id . ' /workHours/' . $workHour->id);
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    public function test_update_work_hours_to_expert_by_user_not_administrator()
    {
        $user = User::factory()->create();
        $workHour = WorkHour::factory()->create();
        $response = $this->actingAs($user)->putJson('experts/' . $workHour->expert_id . ' /workHours/' . $workHour->id);
        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.', 'status_code' => 403]);
    }

    public function test_add_work_hours_by_invalid_value_to_day()
    {
        $admin = $this->getAdministratorUser();
        $workHour = WorkHour::factory()->create();
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $workHour->expert_id . '/workHours/' . $workHour->id,
                [
                    'day' => "50",
                    'from' => $this->faker->time($format = 'H:i'),
                    'to' => $this->faker->time($format = 'H:i'),
                ]
            );
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "day" => ["The selected day is invalid."],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_update_work_hours_to_expert()
    {
        $admin = $this->getAdministratorUser();
        $workHour = WorkHour::factory()->create();
        $day = Days::getRandomValue();
        $from = $this->faker->time($format = 'H:i');
        $to = $this->faker->time($format = 'H:i');
        $response = $this->actingAs($admin)
            ->putJson('experts/' . $workHour->expert_id . '/workHours/' . $workHour->id, [
                'day' => $day,
                'from' => $from,
                'to' => $to
            ]);
        $response->assertStatus(200);
        $workHour = $workHour->refresh();
        $this->assertSame($day, (int)$workHour->day);
        //because we convert to utc
        $this->assertNotSame($from . ':00', $workHour->from);
        $this->assertNotSame($to . ':00', $workHour->to);
        $response->assertJson(['message' => "The expert's work hour updated successfully.", 'status_code' => 200]);
    }


}
