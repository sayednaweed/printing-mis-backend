
<?php

use App\Http\Controllers\api\template\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/user/profile/picture-update', [ProfileController::class, 'updateUserPicture']);
    Route::post('/user/profile/info/update', [ProfileController::class, 'updateUserProfileInfo']);
    Route::delete('/delete/profile-picture', [ProfileController::class, 'deleteProfilePicture']);
});
