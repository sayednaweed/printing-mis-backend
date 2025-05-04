<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use App\Models\UserPermissionSub;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Enums\Permission\ExpensePermissionEnum;
use App\Enums\Permission\InventoryPermissionEnum;

class UserPermissionSeeder extends Seeder
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
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::employees->value
        ]);
        $this->hrEmployeesSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::attendance->value
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::leave->value
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::salaries->value
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::reports->value
        ]);
        $this->hrReportsSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::users->value
        ]);
        $this->hrUsersSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::configurations->value
        ]);
        $this->hrConfigurationsSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::logs->value
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => HrPermissionEnum::audit->value
        ]);
    }
    public function superExpensePermissions()
    {
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => ExpensePermissionEnum::expenses->value
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => ExpensePermissionEnum::reports->value
        ]);
        $this->expenseReportsSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => ExpensePermissionEnum::configurations->value
        ]);
        $this->expenseConfigurationsSubPermissions($userPermission);
    }
    public function superInventoryPermissions()
    {
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => InventoryPermissionEnum::sellers->value
        ]);
        $this->inventorySellersSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => InventoryPermissionEnum::buyers->value
        ]);
        $this->inventoryBuyersSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => InventoryPermissionEnum::sells->value
        ]);
        $this->inventorySellsSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => InventoryPermissionEnum::purchases->value
        ]);
        $this->inventoryPurchaseSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => InventoryPermissionEnum::projects->value
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => InventoryPermissionEnum::warehouse->value
        ]);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => InventoryPermissionEnum::configurations->value
        ]);
        $this->inventoryConfigurationsSubPermissions($userPermission);
        $userPermission = UserPermission::factory()->create([
            "view" => true,
            "edit" => true,
            "delete" => true,
            "add" => true,
            "user_id" => RoleEnum::super->value,
            "permission" => InventoryPermissionEnum::reports->value
        ]);
        $this->inventoryReportsSubPermissions($userPermission);
    }
    public function hrEmployeesSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::HR_EMPLOYEES as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function hrUsersSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::HR_USERS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function hrConfigurationsSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::HR_CONFIGURATIONS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function expenseConfigurationsSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::EXPENSE_CONFIGURATIONS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function hrReportsSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::HR_REPORTS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function expenseReportsSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::HR_REPORTS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function inventorySellersSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::INVENTORY_SELLERS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function inventoryBuyersSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::INVENTORY_BUYERS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function inventorySellsSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::INVENTORY_SELLS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function inventoryPurchaseSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::INVENTORY_PURCHASE as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function inventoryConfigurationsSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::INVENTORY_CONFIGURATIONS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
    public function inventoryReportsSubPermissions($userPermission)
    {
        foreach (SubPermissionEnum::INVENTORY_REPORTS as $id => $role) {
            UserPermissionSub::factory()->create([
                "edit" => true,
                "delete" => true,
                "add" => true,
                "view" => true,
                "user_permission_id" => $userPermission->id,
                "sub_permission_id" => $id,
            ]);
        }
    }
}
