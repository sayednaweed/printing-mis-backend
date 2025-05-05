
<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\PositionController;
use App\Http\Controllers\api\app\hr\general\DepartmentController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/department/store', [DepartmentController::class, "store"]);
    Route::get('/departments', [DepartmentController::class, "departments"]);
    Route::get('/department/{id}', [DepartmentController::class, "department"]);
});
