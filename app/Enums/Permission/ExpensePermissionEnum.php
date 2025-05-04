<?php

namespace App\Enums\Permission;

enum ExpensePermissionEnum: string
{
    case expenses = "expenses";
    case configurations = "exp_configurations";
    case reports = "exp_reports";
}
