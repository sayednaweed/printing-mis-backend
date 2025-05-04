<?php

namespace Database\Seeders;

use App\Enums\LanguageEnum;
use App\Models\User;
use App\Models\Email;
use App\Enums\RoleEnum;
use App\Models\Department;
use App\Models\DepartmentTran;
use App\Models\ModelJob;
use App\Models\ModelJobTrans;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class JobAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $department = Department::factory()->create([]);
        DepartmentTran::factory()->create([
            "value" => "Financial",
            "department_id" => $department->id,
            "language_name" => LanguageEnum::default->value,
        ]);
        DepartmentTran::factory()->create([
            "value" => "مالی",
            "department_id" => $department->id,
            "language_name" => LanguageEnum::farsi->value,
        ]);
        DepartmentTran::factory()->create([
            "value" => "مالي",
            "department_id" => $department->id,
            "language_name" => LanguageEnum::pashto->value,
        ]);
        $job = ModelJob::factory()->create([]);
        ModelJobTrans::factory()->create([
            "value" => "Administrator",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::default->value,
        ]);
        ModelJobTrans::factory()->create([
            "value" => "مدیر اجرایی",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::farsi->value,
        ]);
        ModelJobTrans::factory()->create([
            "value" => "اجرایی مدیر",
            "model_job_id" => $job->id,
            "language_name" => LanguageEnum::pashto->value,
        ]);
        $superEmail =  Email::factory()->create([
            "value" => "super@admin.com"
        ]);

        User::factory()->create([
            "id" => RoleEnum::super->value,
            'full_name' => 'Sayed Naweed Sayedy',
            'username' => 'Sayed Naweed',
            'email_id' =>  $superEmail->id,
            'password' =>  Hash::make("123123123"),
            'status' =>  true,
            'grant_permission' =>  true,
            'role_id' =>  RoleEnum::super,
            'job_id' =>  $job->id,
            'department_id' =>  1,
        ]);
    }
}
