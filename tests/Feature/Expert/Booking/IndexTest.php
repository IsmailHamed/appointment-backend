<?php


namespace Tests\Feature\Expert\Booking;


use App\Models\Booking;
use App\Models\Expert;
use App\Models\User;
use App\Transformers\BookingTransformer;
use App\Transformers\ExpertTransformer;
use Tests\Feature\APITestCase;

class IndexTest extends APITestCase
{
    public function test_get_booking_to_expert_by_user_not_authorized()
    {
        $expert = Expert::factory()->create(['id' => 1]);
        Booking::factory()->count(15)->create(
            ['expert_id' => $expert->id]
        );
        $response = $this->getJson("experts/{$expert->id}/bookings?page=2&per_page=5");
        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized', 'status_code' => 401]);
    }

    public function test_get_bookings_to_expert()
    {
        $user = User::factory()->create();
        $expert = Expert::factory()->create(['id' => 1]);
        Booking::factory()->count(15)->create(
            ['expert_id' => $expert->id]
        );
        $response = $this->actingAs($user)->getJson("experts/{$expert->id}/bookings?page=2&per_page=5");
        $booking = Booking::all()->get(6);
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonFragment($this->dataResponse($booking))
            ->assertJson($this->dataResponsePagination());
    }

    public function test_get_bookings_to_expert_when_user_select_time_zone()
    {
        $time_zone = 'Europe/Madrid';
        $user = User::factory()->create();
        $expert = Expert::factory()->create(['id' => 1]);
        Booking::factory()->count(15)->create(
            ['expert_id' => $expert->id]
        );
        $response = $this->actingAs($user)->getJson("experts/{$expert->id}/bookings?page=2&per_page=5&timeZone={$time_zone}");
        $response->assertStatus(200);
        $responsebooking = json_decode($response->getContent(), true)['data'][0];
        $booking = Booking::find($responsebooking['identifier']);
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
        return $response['data'];
    }

    private function dataResponsePagination()
    {
        return
            ['meta' =>
                ['pagination' =>
                    [
                        'total' => 15,
                        'count' => 5,
                        'per_page' => 5,
                        'current_page' => 2,
                        'total_pages' => 3,
                        'links' =>
                            [
                                'previous' => 'http://localhost/api/experts/1/bookings?page=1',
                                'next' => 'http://localhost/api/experts/1/bookings?page=3'
                            ]
                    ]
                ]];
    }

}
