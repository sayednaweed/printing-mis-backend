
<?php

use App\Http\Controllers\api\app\expense\expenseType\ExpenseTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/expense-types', [ExpenseTypeController::class, 'index']);
    Route::post('/expense-types', [ExpenseTypeController::class, 'store']);
    Route::put('/expense-types', [ExpenseTypeController::class, 'update']);
    Route::get('/expense-types/{id}', [ExpenseTypeController::class, 'edit']);
});
