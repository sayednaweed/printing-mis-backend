
<?php

use App\Models\MaritalStatus;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\template\MediaController;
use App\Http\Controllers\api\template\MaritalStatuController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/maritals', [MaritalStatuController::class, "maritalStatuses"]);
});
