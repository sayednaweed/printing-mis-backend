
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\position\PositionController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    // Route::post('/position/store', [PositionController::class, "store"]);
    Route::get('/positions', [PositionController::class, "positions"]);
    // Route::get('/position/{id}', [PositionController::class, "position"]);
});
