
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\shift\ShiftTypeController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/shift-types', [ShiftTypeController::class, "store"]);
    Route::get('/shift-types', [ShiftTypeController::class, "index"]);
    Route::get('/shift-types/{id}', [ShiftTypeController::class, "edit"]);
    Route::put('/shift-types/{id}', [ShiftTypeController::class, 'update']);
});





