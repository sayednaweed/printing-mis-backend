<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;
use App\Models\RolePermissionSub;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Enums\Permission\ExpensePermissionEnum;
use App\Enums\Permission\InventoryPermissionEnum;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->superHrPermissions();
        $this->superExpensePermissions();
        $this->superInventoryPermissions();
    }
    public function superHrPermissions()
    {
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::employees->value
        ]);
        $this->hrEmployeesSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::attendance->value
        ]);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::leave->value
        ]);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::salaries->value
        ]);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::reports->value
        ]);
        $this->hrReportsSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::users->value
        ]);
        $this->hrUsersSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::configurations->value
        ]);
        $this->hrConfigurationsSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::logs->value
        ]);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => HrPermissionEnum::audit->value
        ]);
    }
    public function superExpensePermissions()
    {
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => ExpensePermissionEnum::expenses->value
        ]);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => ExpensePermissionEnum::reports->value
        ]);
        $this->expenseReportsSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => ExpensePermissionEnum::configurations->value
        ]);
        $this->expenseConfigurationsSubPermissions($rolePermission);
    }
    public function superInventoryPermissions()
    {
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => InventoryPermissionEnum::sellers->value
        ]);
        $this->inventorySellersSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => InventoryPermissionEnum::buyers->value
        ]);
        $this->inventoryBuyersSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => InventoryPermissionEnum::sells->value
        ]);
        $this->inventorySellsSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => InventoryPermissionEnum::purchases->value
        ]);
        $this->inventoryPurchaseSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => InventoryPermissionEnum::projects->value
        ]);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => InventoryPermissionEnum::warehouse->value
        ]);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => InventoryPermissionEnum::configurations->value
        ]);
        $this->inventoryConfigurationsSubPermissions($rolePermission);
        $rolePermission = RolePermission::factory()->create([
            "role" => RoleEnum::super,
            "permission" => InventoryPermissionEnum::reports->value
        ]);
        $this->inventoryReportsSubPermissions($rolePermission);
    }
    public function hrEmployeesSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::HR_EMPLOYEES as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function hrUsersSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::HR_USERS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function hrConfigurationsSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::HR_CONFIGURATIONS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function expenseConfigurationsSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::EXPENSE_CONFIGURATIONS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function hrReportsSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::HR_REPORTS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function expenseReportsSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::HR_REPORTS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function inventorySellersSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::INVENTORY_SELLERS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function inventoryBuyersSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::INVENTORY_BUYERS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function inventorySellsSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::INVENTORY_SELLS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function inventoryPurchaseSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::INVENTORY_PURCHASE as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function inventoryConfigurationsSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::INVENTORY_CONFIGURATIONS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
    public function inventoryReportsSubPermissions($rolePermission)
    {
        foreach (SubPermissionEnum::INVENTORY_REPORTS as $id => $role) {
            RolePermissionSub::factory()->create([
                "role_permission_id" => $rolePermission->id,
                "sub_permission_id" => $id
            ]);
        }
    }
}
