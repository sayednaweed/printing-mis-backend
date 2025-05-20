
<?php

use App\Http\Controllers\api\app\expense\buyer\BuyerController;
use App\Http\Controllers\api\app\expense\expenseType\ExpenseTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/buyer', [BuyerController::class, 'index']);
    Route::post('/buyer', [BuyerController::class, 'store']);
    Route::put('/buyer', [BuyerController::class, 'update']);
    Route::get('/buyer/{id}', [BuyerController::class, 'edit']);
});
