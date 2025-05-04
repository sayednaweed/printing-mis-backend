
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\template\PermissionController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/role-permissions/{id}', [PermissionController::class, "rolePermissions"]);
    Route::get('/epi/user/permissions/{id}', [PermissionController::class, "epiPermissions"])->middleware(["epiHasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_permission->value . ',' . 'view']);
    Route::post('/edit/epi/user/permissions', [PermissionController::class, "editEpiPermissions"])->middleware(["epiHasSubPermission:" . HrPermissionEnum::users->value . "," . SubPermissionEnum::hr_user_permission->value . ',' . 'edit']);
});
