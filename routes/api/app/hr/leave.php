
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\attendance\LeaveController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/employees/leaves', [LeaveController::class, 'leaveList']);
    Route::get('/employees/leave/types', [LeaveController::class, 'leaveTypes']);
    Route::post('/employees/take/leave', [LeaveController::class, 'leaveStore']);
});
