<?php


namespace App\Enums;


use BenSampo\Enum\FlaggedEnum;


final class UserStatus extends FlaggedEnum
{
    const REGISTERED = 1 << 0;
    const ACTIVATED = 1 << 1;
    const VERIFIED = 1 << 2;
}

