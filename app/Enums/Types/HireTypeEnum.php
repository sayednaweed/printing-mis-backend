<?php

namespace App\Enums\Types;

enum HireTypeEnum: int
{
    case contractual = 1;
    case permanent = 2;
    case temporary = 3;
    case internship = 4;
}
