
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\SubPermissionEnum;
use App\Enums\Permission\ExpensePermissionEnum;
use App\Http\Controllers\api\app\expense\icon\IconController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/icons-names', [IconController::class, 'index']);
    Route::get('/icons', [IconController::class, 'index'])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_icon->value . ',' . 'view']);
    Route::post('/icons', [IconController::class, 'store'])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_icon->value . ',' . 'view']);
    Route::put('/icons', [IconController::class, 'update'])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_icon->value . ',' . 'view']);
    Route::get('/icons/{id}', [IconController::class, 'edit'])->middleware(["HasSubPermission:" . ExpensePermissionEnum::configurations->value . "," . SubPermissionEnum::expense_configuration_expense_icon->value . ',' . 'view']);
});
