
<?php

use App\Enums\Permission\HrPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\users\UserController;


Route::prefix('v1')->middleware(["authorized:" . 'epi:api'])->group(function () {
    Route::get('/epi/record/count', [UserController::class, "userCount"])->middleware(["epiHasMainPermission:" . HrPermissionEnum::users->value . ',' . 'view']);
    Route::get('/epi/users', [UserController::class, "users"])->middleware(["epiHasMainPermission:" . HrPermissionEnum::users->value . ',' . 'view']);
    Route::get('/epi/user/{id}', [UserController::class, "user"])->middleware(['checkEpiAccess', "epiHasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_information->value . ',' . 'view']);
    Route::delete('/epi/user/delete/profile-picture/{id}', [UserController::class, 'deleteProfilePicture'])->middleware(['checkEpiAccess', "epiHasMainPermission:" . HrPermissionEnum::users->value . ',' . 'delete']);
    Route::post('/epi/user/update/profile-picture', [UserController::class, 'updateProfilePicture'])->middleware(['checkEpiAccess', "epiHasMainPermission:" . HrPermissionEnum::users->value . ',' . 'edit']);
    Route::post('/epi/user/update/information', [UserController::class, 'updateInformation'])->middleware(['checkEpiAccess', "epiHasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_information->value . ',' . 'edit']);
    Route::post('/epi/user/store', [UserController::class, 'store'])->middleware(["epiHasMainPermission:" . HrPermissionEnum::users->value . ',' . 'add']);
    Route::post('/epi/user/change/account/password', [UserController::class, 'changePassword'])->middleware(['checkEpiAccess', "epiHasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_password->value . ',' . 'edit']);
});
