
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permission\HrPermissionEnum;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\template\JobController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::delete('/job/{id}', [JobController::class, "destroy"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_job->value . ',' . 'delete']);
    Route::post('/job/store', [JobController::class, "store"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_job->value . ',' . 'add']);
    Route::post('/job/update', [JobController::class, "update"])->middleware(["HasSubPermission:" . HrPermissionEnum::configurations->value . "," . SubPermissionEnum::hr_configuration_job->value . ',' . 'edit']);
    Route::get('/jobs', [JobController::class, "jobs"]);
    Route::get('/job/{id}', [JobController::class, "job"]);
});
