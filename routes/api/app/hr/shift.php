
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\general\ShiftController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/shift/store', [ShiftController::class, "store"]);
    Route::get('/shifts', [ShiftController::class, "shifts"]);
    Route::get('/shift/{id}', [ShiftController::class, "shift"]);
});
