
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\shift\ShiftController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/shifts', [ShiftController::class, "store"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_shifts->value . ',' . 'add']);
    Route::get('/shifts', [ShiftController::class, "shifts"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_shifts->value . ',' . 'view']);
    Route::get('/shifts/{id}', [ShiftController::class, "edit"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_shifts->value . ',' . 'view']);
    Route::put('/shifts/{id}', [ShiftController::class, 'update'])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_shifts->value . ',' . 'edit']);
});
