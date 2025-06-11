<?php

namespace App\Enums\Status;

enum StatusEnum: int
{
    // Hr status:
    case hired = 1;
    case resigned = 2;
    case terminated = 3;
    case absconded = 4;
    case deceased = 5;
    case working = 6;
        // User status
    case active = 7;
    case in_active = 8;
        // Leave status
    case sick = 9;
}
