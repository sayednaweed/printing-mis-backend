
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\inventory\party\seller\SellerController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/sellers', [SellerController::class, 'index']);
    Route::post('/sellers', [SellerController::class, 'store']);
    Route::put('/sellers', [SellerController::class, 'update']);
    Route::get('/sellers/{id}', [SellerController::class, 'edit']);
});
