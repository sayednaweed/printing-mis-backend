
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\SubPermissionEnum;
use App\Enums\Permission\ExpensePermissionEnum;
use App\Http\Controllers\api\app\expense\expenseType\ExpenseTypeController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/expense-types', [ExpenseTypeController::class, 'index'])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_type->value . ',' . 'view']);
    Route::post('/expense-types', [ExpenseTypeController::class, 'store'])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_type->value . ',' . 'view']);
    Route::put('/expense-types', [ExpenseTypeController::class, 'update'])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_type->value . ',' . 'view']);
    Route::get('/expense-types/{id}', [ExpenseTypeController::class, 'edit'])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_type->value . ',' . 'view']);
    Route::delete('/expense-types/{id}', [ExpenseTypeController::class, "destroy"])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_type->value . ',' . 'delete']);
});
