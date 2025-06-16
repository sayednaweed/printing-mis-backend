<?php

namespace App\Enums\Types;

enum PaymentTypeEnum: int
{
    case partial_payment = 1; // in-between payment (not necessarily early)
    case final_payment = 2;   // closing payment for the month
    case full_payment = 3;    // single full salary paid in one go
}
