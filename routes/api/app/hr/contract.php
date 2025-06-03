
<?php

use App\Http\Controllers\api\app\hr\employee\contract\ContractController;
use Illuminate\Support\Facades\Route;

Route::get('/generate/employee/contract/{id}', [ContractController::class, 'generateContract']);

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/generate/employee/contract/{id}', [ContractController::class, 'generateContract']);
});
