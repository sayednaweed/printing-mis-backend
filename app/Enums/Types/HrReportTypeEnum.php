<?php

namespace App\Enums\Types;

enum HrReportTypeEnum: string
{
    case salary = 'salary';
    case  attendance = 'attendance';


    public function id(): int
    {
        return match ($this) {
            self::salary => 1,
            self::attendance => 2,
        };
    }
}
