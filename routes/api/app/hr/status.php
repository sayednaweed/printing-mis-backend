
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\employee\status\StatusController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/employee/statuses/{id}', [StatusController::class, 'employeeStatuses']);
    Route::get('/employee/status/list/{id}', [StatusController::class, 'employeeStatusList']);
    Route::post('/update/employent/status/{id}', [StatusController::class, 'statusUpdate']);
});
