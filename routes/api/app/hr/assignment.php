
<?php

use App\Enums\Permission\HrPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\assignment\EmployeeAssignment;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/employee/assigment/store', [EmployeeAssignment::class, 'store'])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_promotion_demotion->value . ',' . 'add']);
    Route::get('/employee/assigments/{id}', [EmployeeAssignment::class, "employeeAssignments"])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_promotion_demotion->value . ',' . 'view']);
    Route::get('/employee/assigments/change/position', [EmployeeAssignment::class, "changePosition"])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_promotion_demotion->value . ',' . 'view']);
});
