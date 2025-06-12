
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Http\Controllers\api\app\hr\salary\PayrollController;

Route::get('/salaries/employee-payment/{hr_code}', [PayrollController::class, "salaryPayment"]);
Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/salaries', [PayrollController::class, 'index'])->middleware(["HasMainPermission:" . HrPermissionEnum::salaries->value . ',' . 'view']);
    Route::get('/salaries/{id}', [PayrollController::class, 'show'])->middleware(["HasMainPermission:" . HrPermissionEnum::salaries->value . ',' . 'view']);
    Route::post('/salaries', [PayrollController::class, 'store'])->middleware(["HasMainPermission:" . HrPermissionEnum::salaries->value . ',' . 'add']);
    Route::get('/salaries-show', [PayrollController::class, 'showAttendance'])->middleware(["HasMainPermission:" . HrPermissionEnum::salaries->value . ',' . 'view']);
    Route::get('/salaries/statuses', [PayrollController::class, 'statuses']);
    Route::get('/salaries/employee-payment/{id}', [PayrollController::class, "salaryPayment"])->middleware(["HasMainPermission:" . HrPermissionEnum::salaries->value . ',' . 'add']);
});
