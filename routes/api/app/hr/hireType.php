
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\hire\HireController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/hr/codes', [HireController::class, "hrCodes"]);
    Route::post('/hire-types', [HireController::class, "store"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_hire_type->value . ',' . 'add']);
    Route::get('/hire-types', [HireController::class, "index"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_hire_type->value . ',' . 'view']);
    Route::get('/hire-types/{id}', [HireController::class, "edit"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_hire_type->value . ',' . 'view']);
    Route::put('/hire-types', [HireController::class, 'update'])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_hire_type->value . ',' . 'edit']);
});
