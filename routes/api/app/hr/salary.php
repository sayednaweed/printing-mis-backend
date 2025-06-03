
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Http\Controllers\api\app\hr\salary\SalaryController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/salaries', [SalaryController::class, 'index'])->middleware(["HasMainPermission:" . HrPermissionEnum::attendance->value . ',' . 'view']);
    Route::get('/salaries/{id}', [SalaryController::class, 'show'])->middleware(["HasMainPermission:" . HrPermissionEnum::attendance->value . ',' . 'view']);
    Route::post('/salaries', [SalaryController::class, 'store'])->middleware(["HasMainPermission:" . HrPermissionEnum::attendance->value . ',' . 'add']);
    Route::get('/salaries-show', [SalaryController::class, 'showAttendance'])->middleware(["HasMainPermission:" . HrPermissionEnum::attendance->value . ',' . 'view']);
    Route::get('/salaries/statuses', [SalaryController::class, 'statuses']);
});
