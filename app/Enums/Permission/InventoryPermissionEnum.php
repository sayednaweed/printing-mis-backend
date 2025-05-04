<?php

namespace App\Enums\Permission;

enum InventoryPermissionEnum: string
{
    case sellers = "sellers";
    case buyers = "buyers";
    case sells = "sells";
    case purchases = "purchases";
    case projects = "projects";
    case warehouse = "warehouse";
    case configurations = "inv_configurations";
    case reports = "inv_reports";
}
