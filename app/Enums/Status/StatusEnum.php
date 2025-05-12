<?php

namespace App\Enums\Status;

enum StatusEnum: int
{
    // While Working:
    case active = 1;
    case on_leave = 2;
        // Upon Leaving:
    case resigned = 3;
    case terminated = 4;
    case absconded = 5;
    case deceased = 6;
}
