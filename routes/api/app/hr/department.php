
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\department\DepartmentController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/department/store', [DepartmentController::class, "store"]);
    Route::get('/departments', [DepartmentController::class, "departments"]);
    Route::get('/department/{id}', [DepartmentController::class, "department"]);
});
