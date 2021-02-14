<?php


namespace App\Enums;


use BenSampo\Enum\FlaggedEnum;


final class ExpertStatus extends FlaggedEnum
{
    const ACTIVATED = 1 << 0;
    const PENDING = 1 << 1;
    const SUSPEND = 1 << 2;
}

