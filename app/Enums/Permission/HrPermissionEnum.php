<?php

namespace App\Enums\Permission;

enum HrPermissionEnum: string
{
    case employees = "employees";
    case attendance = "attendance";
    case leave = "leave";
    case salaries = "salaries";
    case reports = "hr_reports";
    case users = "users";
    case configurations = "hr_configurations";
    case logs = "logs";
    case audit = "audit";
}
