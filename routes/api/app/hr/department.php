
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\department\DepartmentController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/department/store', [DepartmentController::class, "store"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_department->value . ',' . 'add']);
    Route::post('/department/update', [DepartmentController::class, "update"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_department->value . ',' . 'edit']);
    Route::get('/departments', [DepartmentController::class, "departments"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_department->value . ',' . 'view']);
    Route::get('/department/{id}', [DepartmentController::class, "department"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_department->value . ',' . 'view']);
});
