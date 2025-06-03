
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\template\ApplicationController;
use App\Http\Controllers\api\template\ReportTemplateController;

Route::prefix('v1')->group(function () {
    Route::get('/lang/{locale}', [ApplicationController::class, 'changeLocale']);
    Route::get('/system-font/{direction}', [ApplicationController::class, "font"]);
    Route::get('/nid/types', [ApplicationController::class, "nidTypes"]);
    Route::get('/genders', [ApplicationController::class, "genders"]);
    Route::post('/user/validate/email/contact', [ApplicationController::class, "validateEmailContact"]);
    Route::get('/nationalities', [ApplicationController::class, "nationalities"]);
    Route::get('/currencies', [ApplicationController::class, "currencies"]);
    Route::get('/report/selections', [ReportTemplateController::class, 'selections']);
    Route::get('/locales/{lang}/{namespace}', [ApplicationController::class, 'getTranslations']);
});
