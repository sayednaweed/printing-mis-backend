<?php

namespace Database\Seeders;

use App\Models\SubPermission;
use Illuminate\Database\Seeder;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Enums\Permission\ExpensePermissionEnum;
use App\Enums\Permission\InventoryPermissionEnum;

class SubPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->hrSubPermissions();
        $this->expenseSubPermissions();
        $this->inventorySubPermissions();
    }
    public function hrSubPermissions()
    {
        foreach (SubPermissionEnum::HR_EMPLOYEES as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => HrPermissionEnum::employees->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::HR_USERS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => HrPermissionEnum::users->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::HR_CONFIGURATIONS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => HrPermissionEnum::configurations->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::HR_REPORTS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => HrPermissionEnum::reports->value,
                "name" => $role,
            ]);
        }
    }
    public function expenseSubPermissions()
    {
        foreach (SubPermissionEnum::EXPENSE_CONFIGURATIONS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => ExpensePermissionEnum::configurations->value,
                "name" => $role,
            ]);
        }
    }
    public function inventorySubPermissions()
    {
        foreach (SubPermissionEnum::INVENTORY_SELLERS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => InventoryPermissionEnum::sellers->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::INVENTORY_BUYERS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => InventoryPermissionEnum::buyers->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::INVENTORY_SELLS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => InventoryPermissionEnum::sells->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::INVENTORY_PURCHASE as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => InventoryPermissionEnum::purchases->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::INVENTORY_CONFIGURATIONS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => InventoryPermissionEnum::configurations->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::INVENTORY_ACCOUNTS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => InventoryPermissionEnum::reports->value,
                "name" => $role,
            ]);
        }
        foreach (SubPermissionEnum::INVENTORY_REPORTS as $id => $role) {
            SubPermission::factory()->create([
                "id" => $id,
                "permission" => InventoryPermissionEnum::reports->value,
                "name" => $role,
            ]);
        }
    }
}
