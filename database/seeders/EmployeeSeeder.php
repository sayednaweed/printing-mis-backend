<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\CheckList;
use App\Enums\LanguageEnum;
use App\Models\CheckListType;
use App\Models\CheckListTrans;
use Illuminate\Database\Seeder;
use App\Models\CheckListTypeTrans;
use App\Enums\Checklist\CheckListEnum;
use App\Enums\Checklist\CheckListTypeEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $this->checklist();
    }

    protected  function checklist()
    {

        CheckListType::create(['id' => CheckListTypeEnum::employee->value]);
        CheckListTypeTrans::create([
            'check_list_type_id' => CheckListTypeEnum::employee->value,
            'value' => 'Employee',
            'language_name' => 'en',
        ]);
        CheckListTypeTrans::create([
            'check_list_type_id' => CheckListTypeEnum::employee->value,
            'value' => 'کارکوونکی',
            'language_name' => 'ps',
        ]);
        CheckListTypeTrans::create([
            'check_list_type_id' => CheckListTypeEnum::employee->value,
            'value' => 'کارمند',
            'language_name' => 'fa',
        ]);




        $checklist = CheckList::create([
            'id' => CheckListEnum::employee_attachment->value,
            'check_list_type_id' => CheckListTypeEnum::employee->value,
            'acceptable_extensions' => "pdf,jpeg,png,jpg",
            'acceptable_mimes' => "application/pdf,image/jpeg,image/png,image/jpg",
            'accept' => ".pdf,.jpeg,.png,.jpg",
            'description' => "",
            'file_size' => 3048,
            'user_id' => RoleEnum::super,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "Employee Attachment",
            'language_name' => LanguageEnum::default,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "کارکوونکی ضمیمه",
            'language_name' => LanguageEnum::farsi,
        ]);
        CheckListTrans::create([
            'check_list_id' => $checklist->id,
            'value' => "کارمند ضمیمه",
            'language_name' => LanguageEnum::pashto,
        ]);
    }
}
