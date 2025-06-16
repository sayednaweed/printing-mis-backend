
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\ExpensePermissionEnum;
use App\Http\Controllers\api\app\expense\ExpenseController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index'])->middleware(["HasMainPermission:" . ExpensePermissionEnum::expenses->value . ',' . 'view']);
    Route::post('/expenses', [ExpenseController::class, 'store'])->middleware(["HasMainPermission:" . ExpensePermissionEnum::expenses->value . ',' . 'add']);
    Route::put('/expenses', [ExpenseController::class, 'update'])->middleware(["HasMainPermission:" . ExpensePermissionEnum::expenses->value . ',' . 'edit']);
    Route::get('/expenses/{id}', [ExpenseController::class, 'edit'])->middleware(["HasMainPermission:" . ExpensePermissionEnum::expenses->value . ',' . 'view']);
    Route::delete('/expenses/{id}', [ExpenseController::class, "destroy"])->middleware(["HasMainPermission:" . ExpensePermissionEnum::expenses->value . ',' . 'delete']);
});
