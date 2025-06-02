
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\shift\ShiftController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/shift', [ShiftController::class, "store"]);
    Route::get('/shifts', [ShiftController::class, "shifts"]);
    Route::get('/shift/{id}', [ShiftController::class, "edit"]);
    Route::put('/shift/{id}', [ShiftController::class, 'update']);
});
