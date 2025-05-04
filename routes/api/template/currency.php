
<?php

use App\Http\Controllers\api\template\CurrencyController;
use App\Http\Controllers\api\template\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/currencies', [CurrencyController::class, "currencies"]);
    // Route::get('/temp/media', [MediaController::class, "tempMediadownload"]);
});
