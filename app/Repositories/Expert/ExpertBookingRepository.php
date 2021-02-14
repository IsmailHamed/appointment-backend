<?php


namespace App\Repositories\Expert;


use App\Enums\BookingStatus;
use App\Interfaces\Expert\ExpertBookingInterface;
use App\Models\Booking;
use App\Models\Expert;
use App\Traits\ApiResponse;
use App\Traits\FileManagement;
use App\Transformers\BookingTransformer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpertBookingRepository implements ExpertBookingInterface
{
    use ApiResponse;

    protected $model;

    public function __construct(Booking $model)
    {
        $this->model = $model;
    }

    public function all(Expert $expert)
    {
        $bookings = $expert->bookings();
        return $this->showAll($bookings, new BookingTransformer());
    }

    public function show(Booking $booking)
    {
        return $this->showOne($booking, new BookingTransformer());
    }

    public function store(Expert $expert)
    {
        $booking = $this->createBooking();
        $expert->bookings()->save($booking);
        return $this->successMessage("The expert's booking added successfully.");

    }


    public function update(Booking $booking)
    {
        //todo update status with admin or expert has permission
        $finish_at = $this->calculateFinishTime();
        $booking->finish_at = $finish_at;
        $booking->update(request()->all());
        return $this->successMessage("The expert's booking updated successfully.");

    }

    public function delete(Booking $booking)
    {
        $booking->delete();
        return $this->successMessage("The expert's booking deleted successfully.");
    }

    private function calculateFinishTime()
    {
        $start_at = request('start_at');
        $duration = request('duration');
        $finish_at = Carbon::createFromTimestamp(strtotime($start_at))->copy()
            ->addMinutes($duration)->toDateTimeString();
        return $finish_at;
    }

    private function createBooking()
    {
        $user = Auth::user();
        $finish_at = $this->calculateFinishTime();
        $booking = new Booking(request()->all());
        $booking->user_id = $user->id;
        $booking->finish_at = $finish_at;
        $booking->status = BookingStatus::ACCEPTED;
        return $booking;
    }
}
