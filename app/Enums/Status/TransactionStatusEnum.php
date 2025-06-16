<?php

namespace App\Enums\Status;

enum TransactionStatusEnum: int
{
    case pending = 1;
    case completed = 2;
    case cancelled = 3;
    case refunded = 4;
}
