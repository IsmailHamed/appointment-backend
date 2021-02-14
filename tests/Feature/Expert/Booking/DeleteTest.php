<?php


namespace Tests\Feature\Expert\Booking;


use App\Models\Booking;
use App\Models\Expert;
use App\Models\User;
use Tests\Feature\APITestCase;

class DeleteTest extends APITestCase
{
    public function test_delete_booking_to_expert_by_user_not_authorized()
    {
        $expert = Expert::factory()->create();
        $bookings = Booking::factory()->count(2)->create(['expert_id' => $expert->id]);
        $response = $this->deleteJson("experts/{$expert->id}/bookings/{$bookings->first()->id}");
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
        $this->assertCount(2, $expert->bookings);

    }

    public function test_delete_booking_to_expert_by_user_not_administrator()
    {
        $user = User::factory()->create();
        $expert = Expert::factory()->create();
        $bookings = Booking::factory()->count(2)->create(['expert_id' => $expert->id]);
        $response = $this->actingAs($user)->deleteJson("experts/{$expert->id}/bookings/{$bookings->first()->id}");
        $response->assertStatus(403)
            ->assertJson(['message' => 'This action is unauthorized.', 'status_code' => 403]);
        $this->assertCount(2, $expert->bookings);

    }

    public function test_delete_booking_to_expert()
    {
        $admin = $this->getAdministratorUser();
        $expert = Expert::factory()->create();
        $bookings = Booking::factory()->count(2)->create(['expert_id' => $expert->id]);
        $response = $this->actingAs($admin)->deleteJson("experts/{$expert->id}/bookings/{$bookings->first()->id}");
        $response->assertStatus(200);
        $this->assertCount(1, $expert->bookings);
        $response->assertJson(['message' => "The expert's booking deleted successfully.", 'status_code' => 200]);

    }

}
