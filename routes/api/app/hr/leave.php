
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\attendance\LeaveController;

Route::get('/employee/leaves', [LeaveController::class, 'leaveList']);

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/employees/leaves', [LeaveController::class, 'leaveList']);
    Route::get('/leave/types', [LeaveController::class, 'leaveTypes']);
});
