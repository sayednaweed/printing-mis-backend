
<?php

use App\Http\Controllers\api\app\expense\dashboard\ExpenseDashboard;
use Illuminate\Support\Facades\Route;




Route::get('/epi/dashboard/data', [ExpenseDashboard::class, 'dashboard']);
Route::prefix('v1')->middleware(['api.key', "authorized:" . 'epi:api'])->group(function () {});
