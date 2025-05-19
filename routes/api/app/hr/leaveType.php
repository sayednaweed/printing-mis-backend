
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\attendance\LeaveTypeController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/leave-types', [LeaveTypeController::class, 'index']);
});
