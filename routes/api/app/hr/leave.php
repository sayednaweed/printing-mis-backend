
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\attendance\LeaveController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/leaves', [LeaveController::class, 'index']);
    Route::post('/leaves', [LeaveController::class, 'store']);
    Route::put('/leaves', [LeaveController::class, 'update']);
    Route::get('/leaves/{id}', [LeaveController::class, 'edit']);
});
