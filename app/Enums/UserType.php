<?php

namespace App\Enums;

use BenSampo\Enum\Enum;


final class UserType extends Enum
{
    const ADMINISTRATOR = 0;
    const EXPERT = 1;
    const USER = 2;
    const GUEST = 2;
}
