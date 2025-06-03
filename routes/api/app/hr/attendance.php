
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Http\Controllers\api\app\hr\attendance\AttendanceController;



Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/attendancies', [AttendanceController::class, 'index'])->middleware(["HasMainPermission:" . HrPermissionEnum::attendance->value . ',' . 'view']);
    Route::get('/attendancies/{id}', [AttendanceController::class, 'show'])->middleware(["HasMainPermission:" . HrPermissionEnum::attendance->value . ',' . 'view']);
    Route::post('/attendancies', [AttendanceController::class, 'store'])->middleware(["HasMainPermission:" . HrPermissionEnum::attendance->value . ',' . 'add']);
    Route::get('/attendancies-show', [AttendanceController::class, 'showAttendance'])->middleware(["HasMainPermission:" . HrPermissionEnum::attendance->value . ',' . 'view']);
    Route::get('/attendancies/statuses', [AttendanceController::class, 'statuses']);
});
