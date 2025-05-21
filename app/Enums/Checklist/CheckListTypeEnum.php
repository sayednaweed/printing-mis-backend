<?php

namespace App\Enums\Checklist;

enum CheckListTypeEnum: int
{
    case employee = 1;

    case sellers = 2;
    case buyers = 3;

}
