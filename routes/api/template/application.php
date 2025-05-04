
<?php

use App\Http\Controllers\api\template\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/lang/{locale}', [ApplicationController::class, 'changeLocale']);
    Route::get('/system-font/{direction}', [ApplicationController::class, "font"]);
    Route::get('/nid/types', [ApplicationController::class, "nidTypes"]);
    Route::get('/genders', [ApplicationController::class, "genders"]);
    Route::post('/user/validate/email/contact', [ApplicationController::class, "validateEmailContact"]);
    Route::get('/nationalities', [ApplicationController::class, "nationalities"]);
    Route::get('/currencies', [ApplicationController::class, "currencies"]);
    Route::get('/payment/statuses', [ApplicationController::class, "paymentStatuses"]);
});
