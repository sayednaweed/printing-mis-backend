
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\hr\Education\EducationController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/education-levels', [EducationController::class, "educationLevels"]);
});
