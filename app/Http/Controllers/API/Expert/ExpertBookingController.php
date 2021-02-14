<?php

namespace App\Http\Controllers\API\Expert;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expert\Booking\DeleteExpertBookingRequest;
use App\Http\Requests\Expert\Booking\StoreExpertBookingRequest;
use App\Http\Requests\Expert\Booking\UpdateExpertBookingRequest;
use App\Interfaces\Expert\ExpertBookingInterface;
use App\Models\Booking;
use App\Models\Expert;
use App\Transformers\BookingTransformer;

class ExpertBookingController extends Controller
{
    protected $expertBooking;

    public function __construct(ExpertBookingInterface $expertBooking)
    {
        $this->middleware('auth:api');
        $this->middleware('transform.input:' . BookingTransformer::class)->except(['delete']);
        $this->middleware('transformToUTC')->only(['store', 'update']);
        $this->expertBooking = $expertBooking;
    }

    /**
     * Get experts
     * This endpoint lets you to get basic information to experts
     * @responseFile storage/responses/expert/booking/bookings.json
     * @return mixed
     */

    public function index(Expert $expert)
    {
        return $this->expertBooking->all($expert);

    }

    /**
     * Get specific expert
     * This endpoint lets you to get basic information to specific expert by identifier
     * @responseFile storage/responses/expert/booking/index.json
     * @return mixed
     */

    public function show(Expert $expert, Booking $booking)
    {

        return $this->expertBooking->show($booking);
    }

    /**
     * Create expert
     * This endpoint lets you to create new expert
     * @responseFile storage/responses/expert/booking/store.json
     * @return mixed
     */
    public function store(StoreExpertBookingRequest $request, Expert $expert, Booking $booking)
    {
        return $this->expertBooking->store($expert);
    }

    /**
     * Update expert
     * This endpoint lets you to update existing expert by identifier
     * @responseFile storage/responses/expert/booking/update.json
     * @return mixed
     */
    public function update(UpdateExpertBookingRequest $request, Expert $expert, Booking $booking)
    {
        return $this->expertBooking->update($booking);
    }

    /**
     * Delete expert
     * This endpoint lets you to delete existing expert by identifier
     * @responseFile storage/responses/expert/booking/delete.json
     * @return mixed
     */
    public function destroy(DeleteExpertBookingRequest $request, Expert $expert, Booking $booking)
    {
        return $this->expertBooking->delete($booking);
    }
}
