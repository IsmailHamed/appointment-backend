<?php


namespace Tests\Feature\Expert\Booking;


use App\Enums\BookingDuration;
use App\Models\Booking;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Support\Carbon;
use Tests\Feature\APITestCase;

class UpdateTest extends APITestCase
{
    public function test_date_is_required()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $response = $this->actingAs($user)
            ->putJson("experts/{$booking->expert_id}/bookings/{$booking->id}", array_merge($this->requestData(), ['startAt' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "startAt" => ['The start at field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_duration_is_required()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $response = $this->actingAs($user)
            ->putJson("experts/{$booking->expert_id}/bookings/{$booking->id}", array_merge($this->requestData(), ['duration' => '']));
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "duration" => ['The duration field is required.'],
                ],
                "status_code" => '422'
            ]);
    }

    public function test_invalid_date()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $response = $this->actingAs($user)
            ->putJson("experts/{$booking->expert_id}/bookings/{$booking->id}", array_merge($this->requestData(), ['startAt' => '1111']));
        $response
            ->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "startAt" => [
                        'The start at is not a valid date.',
                        'The start at does not match the format Y-m-d H:i:s.',
                        'The start at must be a date after ' . date('Y-m-d H:i') . '.'
                    ],
                ],
                "status_code" => 422
            ]);
    }

    public function test_invalid_duration()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $response = $this->actingAs($user)
            ->putJson("experts/{$booking->expert_id}/bookings/{$booking->id}", array_merge($this->requestData(), ['duration' => 10]));
        $response
            ->assertStatus(422)
            ->assertExactJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "duration" => ['The selected duration is invalid.'],
                ],
                "status_code" => 422
            ]);
    }

    public function test_update_booking_when_user_not_authorized()
    {
        $booking = Booking::factory()->create();
        $response = $this->putJson("experts/{$booking->expert_id}/bookings/{$booking->id}", $this->requestData());
        $response->assertStatus(401)
            ->assertExactJson(['message' => "Unauthorized", 'status_code' => 401]);
    }

    public function test_update_booking()
    {
//        $this->withServerVariables(['REMOTE_ADDR' => '185.225.210.17']);
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $requestData = $this->requestData();
        $response = $this->actingAs($user)->putJson("experts/{$booking->expert_id}/bookings/{$booking->id}", $requestData);
        $response->assertStatus(200)
            ->assertExactJson(['message' => "The expert's booking updated successfully.", 'status_code' => 200]);
        $booking->refresh();
        $startAt = Carbon::createFromDate($requestData['startAt']);
        $duration = $requestData['duration'];
        $finishAt = $startAt->copy()->addMinutes($duration);
        $this->assertEquals($booking->duration, $duration);
        $this->assertEquals((string)$booking->start_at, (string)$startAt);
        $this->assertEquals((string)$booking->finish_at, (string)$finishAt);
    }

    public function test_update_booking_when_user_select_time_zone()
    {
//        $this->withServerVariables(['REMOTE_ADDR' => '185.225.210.17']);
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $time_zone = 'Europe/Madrid';
        $startAt = Carbon::now()->addHours(5)->setTimezone($time_zone);
        $duration = BookingDuration::HOUR;
        $requestData = [
            'timeZone' => $time_zone,
            'startAt' => $startAt->toDateTimeString(),
            'duration' => $duration,
        ];
        $response = $this->actingAs($user)->putJson("experts/{$booking->expert_id}/bookings/{$booking->id}", $requestData);
        $response->assertStatus(200)
            ->assertExactJson(['message' => "The expert's booking updated successfully.", 'status_code' => 200]);
        $booking->refresh();
        $duration = $requestData['duration'];
        $finishAt = $startAt->copy()->addMinutes($duration);
        $this->assertEquals($booking->duration, $duration);
        $this->assertNotEquals((string)$booking->start_at, (string)$startAt);
        $this->assertNotEquals((string)$booking->finish_at, (string)$finishAt);
        $this->assertEquals((string)$booking->start_at, (string)$startAt->setTimezone("UTC"));
        $this->assertEquals((string)$booking->finish_at, (string)$finishAt->setTimezone("UTC"));

    }

//start time of new appointment is between start and end of any record
    public function test_update_booking_where_start_time_between_start_and_end_of_another_booking()
    {
        $user = User::factory()->create();
        $startAt = Carbon::now()->addMinutes(-10);
        $duration = BookingDuration::HOUR;
        $finishAt = $startAt->copy()->addMinutes($duration);
        $previousBooking = Booking::factory()->create([
            'duration' => $duration,
            'start_at' => $startAt->toDateTime(),
            'finish_at' => $finishAt->toDateTime(),
        ]);
        $requestData = [
            'startAt' => date('Y-m-d H:i:s'),
            'duration' => BookingDuration::QUARTER,
        ];
        $updateBooking = Booking::factory()->create(
            ['expert_id' => $previousBooking->expert_id]
        );
        $response = $this->actingAs($user)->putJson("experts/{$updateBooking->expert_id}/bookings/{$updateBooking->id}", $requestData);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "startAt" => ['The expert is unavailable at selected time.find an alternate time.'],
                ],
                "status_code" => '422'
            ]);
    }

//end time of new appointment is between start and end of any record
    public function test_update_booking_where_finish_time_between_start_and_end_of_another_booking()
    {
        $user = User::factory()->create();
        $startAt = Carbon::now()->addMinutes(10);
        $duration = BookingDuration::HOUR;
        $finishAt = $startAt->copy()->addMinutes($duration);
        $previousBooking = Booking::factory()->create([
            'duration' => $duration,
            'start_at' => $startAt->toDateTime(),
            'finish_at' => $finishAt->toDateTime(),
        ]);
        $requestData = [
            'startAt' => date('Y-m-d H:i:s'),
            'duration' => BookingDuration::QUARTER,
        ];
        $updateBooking = Booking::factory()->create(
            ['expert_id' => $previousBooking->expert_id]
        );
        $response = $this->actingAs($user)->putJson("experts/{$updateBooking->expert_id}/bookings/{$updateBooking->id}", $requestData);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "startAt" => ['The expert is unavailable at selected time.find an alternate time.'],
                ],
                "status_code" => '422'
            ]);
    }

//start time of new appointment is before start time of a record
//AND end time of new appointment is after end time of any record
    public function test_update_booking_where_start_time_before_start_and_end_after_another_booking()
    {
        $user = User::factory()->create();
        $startAt = Carbon::now()->addMinutes(10);
        $duration = BookingDuration::QUARTER;
        $finishAt = $startAt->copy()->addMinutes($duration);
        $previousBooking = Booking::factory()->create([
            'duration' => $duration,
            'start_at' => $startAt->toDateTime(),
            'finish_at' => $finishAt->toDateTime(),
        ]);
        $requestData = [
            'startAt' => date('Y-m-d H:i:s'),
            'duration' => BookingDuration::HOUR,
        ];
        $updateBooking = Booking::factory()->create(
            ['expert_id' => $previousBooking->expert_id]
        );
        $response = $this->actingAs($user)->putJson("experts/{$updateBooking->expert_id}/bookings/{$updateBooking->id}", $requestData);
        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    "startAt" => ['The expert is unavailable at selected time.find an alternate time.'],
                ],
                "status_code" => '422'
            ]);
    }

    private function requestData()
    {
        return [
            'startAt' => date('Y-m-d H:i:s'),
            'duration' => BookingDuration::getRandomValue(),
        ];
    }

}
