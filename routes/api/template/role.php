
<?php

use App\Http\Controllers\api\template\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/roles', [RoleController::class, "roles"]);
});
