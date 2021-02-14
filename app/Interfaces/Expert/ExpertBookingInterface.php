<?php


namespace App\Interfaces\Expert;


use App\Models\Booking;
use App\Models\Expert;

interface ExpertBookingInterface
{
    public function all(Expert $expert);

    public function store(Expert $expert);

    public function show(Booking $booking);

    public function update(Booking $booking);

    public function delete(Booking $booking);


}
