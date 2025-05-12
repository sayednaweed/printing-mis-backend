
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\attendance\AttendanceController;



Route::get('/attendance/employee', [AttendanceController::class, 'employeeList']);
Route::get('/attendances', [AttendanceController::class, 'attendaceList']);

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/attendance/employees', [AttendanceController::class, 'employeeList']);
    Route::post('/attendace/store', [AttendanceController::class, 'store']);
    Route::get('/attendances', [AttendanceController::class, 'attendaceList']);
    Route::get('/attendance/statuses', [AttendanceController::class, 'statuses']);
});
