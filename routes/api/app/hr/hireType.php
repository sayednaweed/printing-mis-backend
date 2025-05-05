
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\hire\HireController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/hire/type/store', [HireController::class, "store"]);
    Route::get('/hire-types', [HireController::class, "hireTypes"]);
    Route::get('/hire/type/{id}', [HireController::class, "hireType"]);
});
