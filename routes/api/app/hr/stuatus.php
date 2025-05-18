
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\employee\EmployeeController;
use App\Http\Controllers\api\app\hr\employee\status\StatusController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {



    Route::get('/employee/statuses/{id}', [EmployeeController::class, 'employeeStatuses']);

    Route::get('/employee/status/list/{id}', [StatusController::class, 'employeeStatusList']);


    Route::post('/employee/status/update', [StatusController::class, 'statusUpdate']);
});
