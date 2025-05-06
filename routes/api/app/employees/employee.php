
<?php

use App\Enums\Permission\HrPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\employee\EmployeeController;

Route::get('/employee/{id}', [EmployeeController::class, "personalDetial"]);
Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/employees/record/count', [EmployeeController::class, "employeesCount"])->middleware(["HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'view']);
    Route::get('/employees', [EmployeeController::class, "employees"])->middleware(["HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'view']);
    Route::get('/epi/user/{id}', [EmployeeController::class, "user"])->middleware(['checkUserAccess', "HasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_information->value . ',' . 'view']);
    Route::delete('/epi/user/delete/profile-picture/{id}', [EmployeeController::class, 'deleteProfilePicture'])->middleware(['checkUserAccess', "HasSubPermission:" . HrPermissionEnum::users->value . ',' . 'delete']);
    Route::post('/epi/user/update/profile-picture', [EmployeeController::class, 'updateProfilePicture'])->middleware(['checkUserAccess', "HasMainPermission:" . HrPermissionEnum::users->value . ',' . 'edit']);
    Route::post('/epi/user/update/information', [EmployeeController::class, 'updateInformation'])->middleware(['checkUserAccess', "HasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_information->value . ',' . 'edit']);
    Route::post('/employee/store', [EmployeeController::class, 'store'])->middleware(["HasMainPermission:" . HrPermissionEnum::employees->value . ',' . 'add']);
    Route::post('/epi/user/change/account/password', [EmployeeController::class, 'changePassword'])->middleware(['checkUserAccess', "HasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_password->value . ',' . 'edit']);
});
