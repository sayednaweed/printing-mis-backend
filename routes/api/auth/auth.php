<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\auth\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});
Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,finance:api,epi:api'])->group(function () {
    Route::post('/profile/change-password', [AuthController::class, 'changePassword']);
});
