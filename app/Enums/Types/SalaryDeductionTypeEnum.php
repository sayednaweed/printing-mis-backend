<?php

namespace App\Enums\Types;

enum SalaryDeductionTypeEnum: int
{
    case employement = 1;
    case user_status = 2;
    case leave_type = 3;
    case payment_type = 4;
}
