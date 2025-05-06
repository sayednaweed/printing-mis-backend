<?php

namespace App\Enums\Attendance;

enum AttendanceStatusEnum: int
{
    case present = 1;
    case absent = 2;
    case leave = 3;
    case sick = 4;
    // case employee_nid = 2;
}
