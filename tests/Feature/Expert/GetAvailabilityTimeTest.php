<?php


namespace Tests\Feature\Expert;


use App\Enums\BookingDuration;
use App\Enums\Days;
use App\Models\Booking;
use App\Models\Expert;
use App\Models\User;
use App\Models\WorkHour;
use Tests\Feature\APITestCase;

class GetAvailabilityTimeTest extends APITestCase
{
    public function test_get_availability_time_to_expert_by_user_not_authorized()
    {
        $expert = Expert::factory()->create();
        WorkHour::factory([
            'expert_id' => $expert->id,
            'day' => Days::ALL,
            'from' => '13:00',
            'to' => '14:00'
        ])->create();

        $response = $this->postJson("experts/{$expert->id}/get-availability-time", $this->requestData());
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }
    public function test_get_availability_time_to_expert()
    {
        $user = User::factory()->create();
        $expert = Expert::factory()->create();
        WorkHour::factory([
            'expert_id' => $expert->id,
            'day' => Days::ALL,
            'from' => '13:00',
            'to' => '14:00'
        ])->create();

        $response = $this->actingAs($user)->postJson("experts/{$expert->id}/get-availability-time", $this->requestData());
        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    public function test_get_availability_time_to_expert1()
    {
        $user = User::factory()->create();
        $expert = Expert::factory()->create();
        Booking::factory()->create(
            [
                'expert_id' => $expert->id,
                'duration' => BookingDuration::QUARTER,
                'start_at' => now()->format('Y-m-d') . ' 13:00'
            ]
        );
        Booking::factory()->create(
            [
                'expert_id' => $expert->id,
                'duration' => BookingDuration::QUARTER,
                'start_at' => now()->format('Y-m-d') . ' 13:15'
            ]
        );

        WorkHour::factory([
            'expert_id' => $expert->id,
            'day' => Days::ALL,
            'from' => '13:00',
            'to' => '14:00'
        ])->create();

        $response = $this->actingAs($user)->postJson("experts/{$expert->id}/get-availability-time", $this->requestData());
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }


    private function requestData()
    {
        return [
            'date' => now()->format('Y-m-d')
        ];
    }
}
