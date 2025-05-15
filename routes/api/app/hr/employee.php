
<?php

use App\Enums\Permission\HrPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Enums\Permission\SubPermissionEnum;
use App\Http\Controllers\api\app\hr\employee\EmployeeController;
use App\Models\Employee;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    // Main
    Route::post('/employee/store', [EmployeeController::class, 'store'])->middleware(["HasMainPermission:" . HrPermissionEnum::employees->value . ',' . 'add']);
    Route::get('/employees/record/count', [EmployeeController::class, "employeesCount"])->middleware(["HasMainPermission:" . HrPermissionEnum::employees->value . ',' . 'view']);
    Route::get('/employees', [EmployeeController::class, "employees"])->middleware(["HasMainPermission:" . HrPermissionEnum::employees->value . ',' . 'view']);

    // Sub
    Route::get('/employee/{id}', [EmployeeController::class, "personalDetial"])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_information->value . ',' . 'view']);
    Route::get('/employee/more/details/{id}', [EmployeeController::class, "personalMoreDetial"])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_information->value . ',' . 'view']);
    Route::POST('/employee/more/details/update', [EmployeeController::class, "updatePersonalMoreDetail"])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_information->value . ',' . 'edit']);
    Route::delete('/employee/delete/profile-picture/{id}', [EmployeeController::class, 'deleteProfilePicture'])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_information->value . ',' . 'delete']);
    Route::post('/employee/update/profile-picture', [EmployeeController::class, 'updateProfilePicture'])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_information->value . ',' . 'edit']);
    Route::post('/employee/update/information', [EmployeeController::class, 'updatePersonalDetail'])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_information->value . ',' . 'edit']);
    Route::get('/employee/statuses/{id}', [EmployeeController::class, 'employeeStatuses']);
    Route::post('/employee/update/more/details', [EmployeeController::class, 'updatePersonalDetail'])->middleware(["HasSubPermission:" . HrPermissionEnum::employees->value . "," . SubPermissionEnum::hr_employees_information->value . ',' . 'edit']);
});
