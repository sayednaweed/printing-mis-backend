
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\inventory\party\buyer\BuyerController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/buyers', [BuyerController::class, 'index']);
    Route::post('/buyers', [BuyerController::class, 'store']);
    Route::put('/buyers', [BuyerController::class, 'update']);
    Route::get('/buyers/{id}', [BuyerController::class, 'edit']);
});
