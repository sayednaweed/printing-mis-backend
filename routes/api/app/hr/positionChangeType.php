
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\assignment\PositionChangeController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/position/change-types', [PositionChangeController::class, "positionChangeTypes"]);
});
