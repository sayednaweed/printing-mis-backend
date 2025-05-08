
<?php

use App\Enums\Permission\HrPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\users\UserController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/users/record/count', [UserController::class, "userCount"])->middleware(["HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'view']);
    Route::get('/users', [UserController::class, "users"])->middleware(["HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'view']);
    Route::get('/user/{id}', [UserController::class, "user"])->middleware(["HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'view']);
    Route::delete('/user/delete/profile-picture/{id}', [UserController::class, 'deleteProfilePicture'])->middleware(['checkUserAccess', "HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'delete']);
    Route::post('/user/update/profile-picture', [UserController::class, 'updateProfilePicture'])->middleware(['checkUserAccess', "HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'edit']);
    Route::post('/user/update/information', [UserController::class, 'updateInformation'])->middleware(["HasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_information->value . ',' . 'edit']);
    Route::post('/user/store', [UserController::class, 'store'])->middleware(["HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'add']);
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware(["HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'delete']);
    Route::post('/user/validate/email/contact', [UserController::class, "validateEmailContact"]);
    Route::post('/user/account/change-password', [UserController::class, 'changePassword'])->middleware(["HasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_password->value . ',' . 'edit']);
});
