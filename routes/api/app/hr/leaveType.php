
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\attendance\LeaveTypeController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/leave-types', [LeaveTypeController::class, 'index'])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_leave_type->value . ',' . 'view']);
    Route::post('/leave-types', [LeaveTypeController::class, 'store'])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_leave_type->value . ',' . 'add']);
    Route::put('/leave-types', [LeaveTypeController::class, 'update'])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_leave_type->value . ',' . 'edit']);
    Route::get('/leave-types/{id}', [LeaveTypeController::class, 'edit'])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_leave_type->value . ',' . 'view']);
});
