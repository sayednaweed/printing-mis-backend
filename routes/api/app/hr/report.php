
<?php

use App\Http\Controllers\api\app\hr\report\ReportController;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
Route::get('/hr/report/types', [ReportController::class, 'reportTypes']);

});







