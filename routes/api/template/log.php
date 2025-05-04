
<?php

use App\Enums\Permission\HrPermissionEnum;
use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use App\Http\Controllers\api\template\log\UserLoginLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\template\LogController;





Route::prefix('v1')->middleware("authorized:" . 'user:api')->group(function () {
    Route::get('/file-logs', [LogController::class, "fileLogs"]);
    Route::get('/database-logs', [LogController::class, "databaseLogs"])->middleware(["userHasMainViewPermission:" . HrPermissionEnum::logs->value]);
    Route::post('/logs/clear', [LogController::class, "clear"])->middleware(["userHasMainDeletePermission:" . HrPermissionEnum::logs->value]);
    // Route::get('/user-activities', [UserLoginLogController::class, "logs"])->middleware(["userHasSubViewPermission:" . PermissionEnum::activity->value . "," . SubPermissionEnum::user_activity->value]);
});
