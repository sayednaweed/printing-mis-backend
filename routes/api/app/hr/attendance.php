
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\attendance\AttendaceController;



Route::get('/attendace/employees', [AttendaceController::class, 'employeeList']);

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/attendace/employees', [AttendaceController::class, 'employeeList']);
});
