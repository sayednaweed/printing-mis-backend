
<?php

use App\Http\Controllers\api\template\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/media/profile', [MediaController::class, "downloadProfile"]);
    Route::get('/temp/media', [MediaController::class, "tempMediadownload"]);
});
Route::prefix('v1')->group(function () {
    Route::get('/media/public', [MediaController::class, "downloadPublicFile"]);
    Route::get('/public', [MediaController::class, "downloadPublic"]);
});
