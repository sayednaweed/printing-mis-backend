<?php

namespace Database\Seeders;

use App\Enums\Permission\ExpensePermissionEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use App\Enums\Permission\PortalEnum;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\InventoryPermissionEnum;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hrPermissions = [
            ['name' => HrPermissionEnum::employees->value, 'icon' => 'icons/employees.svg', 'priority' => 1, 'portal' => PortalEnum::hr->value],
            ['name' => HrPermissionEnum::attendance->value, 'icon' => 'icons/attendance.svg', 'priority' => 2, 'portal' => PortalEnum::hr->value],
            ['name' => HrPermissionEnum::leave->value, 'icon' => 'icons/leave.svg', 'priority' => 3, 'portal' => PortalEnum::hr->value],
            ['name' => HrPermissionEnum::salaries->value, 'icon' => 'icons/salary.svg', 'priority' => 4, 'portal' => PortalEnum::hr->value],
            ['name' => HrPermissionEnum::reports->value, 'icon' => 'icons/reports.svg', 'priority' => 5, 'portal' => PortalEnum::hr->value],
            ['name' => HrPermissionEnum::users->value, 'icon' => 'icons/users-group.svg', 'priority' => 6, 'portal' => PortalEnum::hr->value],
            ['name' => HrPermissionEnum::configurations->value, 'icon' => 'icons/configurations.svg', 'priority' => 7, 'portal' => PortalEnum::hr->value],
            ['name' => HrPermissionEnum::logs->value, 'icon' => 'icons/logs.svg', 'priority' => 8, 'portal' => PortalEnum::hr->value],
            ['name' => HrPermissionEnum::audit->value, 'icon' => 'icons/audits.svg', 'priority' => 9, 'portal' => PortalEnum::hr->value],
        ];

        $expensePermissions = [
            ['name' => ExpensePermissionEnum::expenses->value, 'icon' => 'icons/salary.svg', 'priority' => 1, 'portal' => PortalEnum::expense->value],
            ['name' => ExpensePermissionEnum::configurations->value, 'icon' => 'icons/configurations.svg', 'priority' => 2, 'portal' => PortalEnum::expense->value],
            ['name' => ExpensePermissionEnum::reports->value, 'icon' => 'icons/reports.svg', 'priority' => 3, 'portal' => PortalEnum::expense->value],
        ];
        $inventoryPermissions = [
            ['name' => InventoryPermissionEnum::sellers->value, 'icon' => 'icons/employees.svg', 'priority' => 1, 'portal' => PortalEnum::inventory->value],
            ['name' => InventoryPermissionEnum::buyers->value, 'icon' => 'icons/employees.svg', 'priority' => 2, 'portal' => PortalEnum::inventory->value],
            ['name' => InventoryPermissionEnum::sells->value, 'icon' => 'icons/transactions.svg', 'priority' => 3, 'portal' => PortalEnum::inventory->value],
            ['name' => InventoryPermissionEnum::purchases->value, 'icon' => 'icons/transactions.svg', 'priority' => 4, 'portal' => PortalEnum::inventory->value],
            ['name' => InventoryPermissionEnum::projects->value, 'icon' => 'icons/projects.svg', 'priority' => 5, 'portal' => PortalEnum::inventory->value],
            ['name' => InventoryPermissionEnum::warehouse->value, 'icon' => 'icons/inventory.svg', 'priority' => 6, 'portal' => PortalEnum::inventory->value],
            ['name' => InventoryPermissionEnum::configurations->value, 'icon' => 'icons/configurations.svg', 'priority' => 7, 'portal' => PortalEnum::inventory->value],
            ['name' => InventoryPermissionEnum::reports->value, 'icon' => 'icons/reports.svg', 'priority' => 8, 'portal' => PortalEnum::inventory->value],
        ];

        foreach ($hrPermissions as $permission) {
            Permission::factory()->create($permission);
        }
        foreach ($expensePermissions as $permission) {
            Permission::factory()->create($permission);
        }
        foreach ($inventoryPermissions as $permission) {
            Permission::factory()->create($permission);
        }
    }
}
