<?php

namespace App\Enums;

enum RoleEnum: int
{
    case super = 1;
    case admin = 2;
    case user = 3;
}
