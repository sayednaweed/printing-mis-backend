
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\template\MaritalStatuController;

Route::prefix('v1')->group(function () {
    Route::get('/marital-statuses', [MaritalStatuController::class, "maritalStatuses"]);
});
