
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\InventoryPermissionEnum;
use App\Http\Controllers\api\app\hr\account\AccountController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/accounts', [AccountController::class, 'index'])->middleware(["HasMainPermission:" . InventoryPermissionEnum::accounts->value . ',' . 'view']);
    Route::post('/accounts', [AccountController::class, 'store'])->middleware(["HasMainPermission:" . InventoryPermissionEnum::accounts->value . ',' . 'add']);
    Route::put('/accounts', [AccountController::class, 'update'])->middleware(["HasMainPermission:" . InventoryPermissionEnum::accounts->value . ',' . 'edit']);
    Route::get('/accounts/{id}', [AccountController::class, 'edit'])->middleware(["HasMainPermission:" . InventoryPermissionEnum::accounts->value . ',' . 'view']);
    Route::get('/accounts-names', [AccountController::class, 'names']);
});
