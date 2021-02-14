<?php


namespace App\Enums;

use BenSampo\Enum\FlaggedEnum;

final class BookingStatus extends FlaggedEnum
{
    const PENDING = 1 << 0;
    const ACCEPTED = 1 << 1;
    const CANCELLED = 1 << 3;
    const COMPLETED = 1 << 4;
    const REJECTED = 1 << 5;
}

