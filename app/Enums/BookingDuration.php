<?php


namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\FlaggedEnum;

final class BookingDuration extends Enum
{
    const QUARTER = 15;
    const HALF = 30;
    const THREEQUARTERS = 45;
    const HOUR = 60;
}

