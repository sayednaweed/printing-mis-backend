<?php

namespace App\Enums\Status;

enum StatusEnum: int
{
    // While Working:
    case hired = 1;
    case on_leave = 2;
        // Upon Leaving:
    case resigned = 3;
    case terminated = 4;
    case absconded = 5;
    case deceased = 6;
        // User status
    case active = 7;
    case in_active = 8;
}
