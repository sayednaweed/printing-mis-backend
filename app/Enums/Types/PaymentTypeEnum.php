<?php

namespace App\Enums\Types;

enum PaymentTypeEnum: int
{
    case advance_payment = 1; // early part of salary
    case partial_payment = 2; // in-between payment (not necessarily early)
    case final_payment = 3;   // closing payment for the month
    case full_payment = 4;    // single full salary paid in one go
}
