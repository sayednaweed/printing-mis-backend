<?php

namespace App\Enums\Types;

enum PayPeriodsTypeEnum: int
{
    case monthly = 1;
    case weekly = 2;
    case biweekly = 3;
}
