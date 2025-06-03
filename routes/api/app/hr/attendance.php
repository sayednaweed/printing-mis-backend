
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\attendance\AttendanceController;



Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/attendancies', [AttendanceController::class, 'index']);
    Route::get('/attendancies/{id}', [AttendanceController::class, 'show']);
    Route::post('/attendancies', [AttendanceController::class, 'store']);
    Route::get('/attendancies-show', [AttendanceController::class, 'showAttendance']);
    Route::get('/attendancies/statuses', [AttendanceController::class, 'statuses']);
});
