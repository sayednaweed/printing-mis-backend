
<?php

use App\Http\Controllers\api\app\hr\payment\PaymentTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/payment-types', [PaymentTypeController::class, 'index']);
    Route::get('/payment-types/names', [PaymentTypeController::class, 'names']);
});
