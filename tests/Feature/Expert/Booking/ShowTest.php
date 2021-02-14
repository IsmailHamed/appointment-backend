<?php


namespace Tests\Feature\Expert\Booking;


use App\Models\Booking;
use App\Models\Expert;
use App\Models\User;
use App\Transformers\BookingTransformer;
use Tests\Feature\APITestCase;

class ShowTest extends APITestCase
{
    public function test_get_booking_to_expert_by_id_by_user_not_authorized()
    {
        $expert = Expert::factory()->create();
        $bookings = Booking::factory()->count(2)->create(['expert_id' => $expert->id]);
        $response = $this->getJson("experts/{$expert->id}/bookings/{$bookings->first()->id}");
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    public function test_get_booking_to_expert_by_id()
    {
        $user = User::factory()->create();
        $expert = Expert::factory()->create();
        $bookings = Booking::factory()->count(2)->create(['expert_id' => $expert->id]);
        $response = $this->actingAs($user)->getJson("experts/{$expert->id}/bookings/{$bookings->first()->id}");
        $response->assertStatus(200)
            ->assertExactJson($this->dataResponse($bookings->first()));
    }

    public function test_get_booking_to_expert_by_id_when_user_select_time_zone()
    {
        $time_zone = 'Europe/Madrid';
        $user = User::factory()->create();
        $expert = Expert::factory()->create();
        $bookings = Booking::factory()->count(2)->create(['expert_id' => $expert->id]);
        $booking = $bookings->first();
        $response = $this->actingAs($user)->getJson("experts/{$expert->id}/bookings/{$booking->id}?timeZone={$time_zone}");
        $response->assertStatus(200);
        $responsebooking = json_decode($response->getContent(), true)['data'];
        $this->assertNotEquals((string)$booking->start_at, $responsebooking['startAt']);
        $this->assertNotEquals((string)$booking->finish_at, $responsebooking['finishAt']);
        $startAt = $this->convertDateFromTimeZoneToUTC($responsebooking['startAt'], $time_zone);
        $finishAt = $this->convertDateFromTimeZoneToUTC($responsebooking['finishAt'], $time_zone);
        $this->assertEquals((string)$booking->start_at, (string)$startAt);
        $this->assertEquals((string)$booking->finish_at, (string)$finishAt);
    }

    private function dataResponse(Booking $booking)
    {
        $response = $this->transformData($booking, new BookingTransformer());
        return $response;
    }

}
