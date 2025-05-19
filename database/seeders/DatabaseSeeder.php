<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Gender;
use App\Models\Status;
use App\Models\NidType;
use App\Models\Currency;
use App\Models\Language;
use App\Models\ShiftTran;
use App\Models\StatusTran;
use App\Models\StatusType;
use App\Models\CurrencyTran;
use App\Models\NidTypeTrans;
use App\Models\MaritalStatus;
use App\Models\EducationLevel;
use App\Models\EmployeeStatus;
use App\Models\ReportSelection;
use Illuminate\Database\Seeder;
use App\Enums\Status\StatusEnum;
use App\Models\MaritalStatusTran;
use App\Models\EducationLevelTran;
use App\Enums\Types\StatusTypeEnum;
use App\Models\ReportSelectionTrans;
use Database\Seeders\CheckListSeeder;
use App\Enums\Types\EducationLevelEnum;
use App\Enums\Types\EmployeeStatusEnum;
use App\Enums\Types\ReportSelectionEnum;
use App\Models\ApplicationConfiguration;
use App\Models\ApplicationConfigurationTrans;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;

/*
1. If you add new Role steps are:
    1. Add to following:
        - RoleEnum
        - RoleSeeder
        - RolePermissionSeeder (Define which permissions role can access)
        - Optional: Set Role on User go to JobAndUserSeeder Then UserPermissionSeeder


2. If you add new Permission steps are:
    1. Add to following:
        - PermissionEnum
        - SubPermissionEnum (In case has Sub Permissions)
        - PermissionSeeder
        - SubPermissionSeeder Then SubPermissionEnum (I has any sub permissions) 
        - RolePermissionSeeder (Define Which Role can access the permission)
        - Optional: Set Permission on User go to JobAndUserSeeder Then UserPermissionSeeder

        
3. If you add new Sub Permission steps are:
    1. Add to following:
        - SubPermissionEnum
        - SubPermissionSeeder
        - RolePermissionSeeder (Define Which Role can access the permission)
        - Optional: Set Permission on User go to JobAndUserSeeder Then UserPermissionSeeder
*/

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->languages();
        $this->gender();
        $this->call(CountrySeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(SubPermissionSeeder::class);
        $this->call(JobAndUserSeeder::class);
        $this->call(CheckListSeeder::class);
        $this->call(UserPermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(HireTypeSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(AttendanceStatusSeeder::class);


        $this->shifts();
        $this->nidTypes();
        $this->maritalStatus();
        $this->currency();
        $this->statusType();
        $this->status();
        $this->Userstatus();
        $this->educationLevel();
        $this->reportSelection();
    }

    public function reportSelection()
    {
        $level = ReportSelection::factory()->create([
            'id' => ReportSelectionEnum::individual->value
        ]);
        ReportSelectionTrans::factory()->create([
            "value" => "Individual",
            "report_selection_id" => $level->id,
            "language_name" => "en",
        ]);
        ReportSelectionTrans::factory()->create([
            "value" => "فردی",
            "report_selection_id" => $level->id,
            "language_name" => "fa",
        ]);
        ReportSelectionTrans::factory()->create([
            "value" => "انفرادي",
            "report_selection_id" => $level->id,
            "language_name" => "ps",
        ]);
        $level = ReportSelection::factory()->create([
            'id' => ReportSelectionEnum::all->value
        ]);
        ReportSelectionTrans::factory()->create([
            "value" => "All",
            "report_selection_id" => $level->id,
            "language_name" => "en",
        ]);
        ReportSelectionTrans::factory()->create([
            "value" => "همه",
            "report_selection_id" => $level->id,
            "language_name" => "fa",
        ]);
        ReportSelectionTrans::factory()->create([
            "value" => "ټول",
            "report_selection_id" => $level->id,
            "language_name" => "ps",
        ]);
    }
    public function educationLevel()
    {
        $level = EducationLevel::factory()->create([
            'id' => EducationLevelEnum::high_school->value
        ]);
        EducationLevelTran::factory()->create([
            "value" => "High School",
            "education_level_id" => $level->id,
            "language_name" => "en",
        ]);
        EducationLevelTran::factory()->create([
            "value" => "لیسه",
            "education_level_id" => $level->id,
            "language_name" => "fa",
        ]);
        EducationLevelTran::factory()->create([
            "value" => "لیسه",
            "education_level_id" => $level->id,
            "language_name" => "ps",
        ]);
        $level = EducationLevel::factory()->create([
            'id' => EducationLevelEnum::bachelor->value
        ]);
        EducationLevelTran::factory()->create([
            "value" => "Bachelor's",
            "education_level_id" => $level->id,
            "language_name" => "en",
        ]);
        EducationLevelTran::factory()->create([
            "value" => "لیسانس",
            "education_level_id" => $level->id,
            "language_name" => "fa",
        ]);
        EducationLevelTran::factory()->create([
            "value" => "لیسانس",
            "education_level_id" => $level->id,
            "language_name" => "ps",
        ]);
        $level = EducationLevel::factory()->create([
            'id' => EducationLevelEnum::master->value
        ]);
        EducationLevelTran::factory()->create([
            "value" => "Master's",
            "education_level_id" => $level->id,
            "language_name" => "en",
        ]);
        EducationLevelTran::factory()->create([
            "value" => "ماستری",
            "education_level_id" => $level->id,
            "language_name" => "fa",
        ]);
        EducationLevelTran::factory()->create([
            "value" => "ماسټر",
            "education_level_id" => $level->id,
            "language_name" => "ps",
        ]);
        $level = EducationLevel::factory()->create([
            'id' => EducationLevelEnum::doctorate->value
        ]);
        EducationLevelTran::factory()->create([
            "value" => "Doctorate",
            "education_level_id" => $level->id,
            "language_name" => "en",
        ]);
        EducationLevelTran::factory()->create([
            "value" => "دکتورا",
            "education_level_id" => $level->id,
            "language_name" => "fa",
        ]);
        EducationLevelTran::factory()->create([
            "value" => "دکتورا",
            "education_level_id" => $level->id,
            "language_name" => "ps",
        ]);
    }
    public function statusType()
    {
        $status = StatusType::factory()->create([
            'id' => StatusTypeEnum::employement->value,
        ]);
        $status = StatusType::factory()->create([
            'id' => StatusTypeEnum::user_status->value,
        ]);
        $status = StatusType::factory()->create([
            'id' => StatusTypeEnum::leave_type->value,
        ]);
    }
    public function Userstatus() {}
    public function status()
    {
        $status = Status::factory()->create([
            'id' => StatusEnum::hired->value,
            'status_type_id' => StatusTypeEnum::employement->value,
        ]);
        StatusTran::factory()->create([
            "value" => "Hired",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "استخدام شده",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "ګمارل شوی",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
        $status = Status::factory()->create([
            'id' => StatusEnum::resigned->value,
            'status_type_id' => StatusTypeEnum::employement->value,
        ]);
        StatusTran::factory()->create([
            "value" => "Resigned",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "استعفا داد",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "استعفا ورکړه",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
        $status = Status::factory()->create([
            'id' => StatusEnum::terminated->value,
            'status_type_id' => StatusTypeEnum::employement->value,
        ]);
        StatusTran::factory()->create([
            "value" => "Terminated",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "اخراج شد",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "اخراج شو",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
        $status = Status::factory()->create([
            'id' => StatusEnum::absconded->value,
            'status_type_id' => StatusTypeEnum::employement->value,
        ]);
        StatusTran::factory()->create([
            "value" => "Absconded",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "غایب شد",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "غایب شوی دی",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
        $status = Status::factory()->create([
            'id' => StatusEnum::deceased->value,
            'status_type_id' => StatusTypeEnum::employement->value,
        ]);
        StatusTran::factory()->create([
            "value" => "Deceased",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "وفات کرده",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "وفات شوی دی",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
        $status = Status::factory()->create([
            'id' => StatusEnum::working->value,
            'status_type_id' => StatusTypeEnum::employement->value,
        ]);
        StatusTran::factory()->create([
            "value" => "Working",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "کار کردن",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "کار کول",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
        $status = Status::factory()->create([
            'id' => StatusEnum::active->value,
            'status_type_id' => StatusTypeEnum::user_status->value,
        ]);
        StatusTran::factory()->create([
            "value" => "Active",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "فعال",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "فعال",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
        $status = Status::factory()->create([
            'id' => StatusEnum::in_active->value,
            'status_type_id' => StatusTypeEnum::user_status->value,
        ]);
        StatusTran::factory()->create([
            "value" => "InActive",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "غیرفعال",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "غیرفعال",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
        $status = Status::factory()->create([
            'id' => StatusEnum::sick->value,
            'status_type_id' => StatusTypeEnum::leave_type->value,
        ]);
        StatusTran::factory()->create([
            "value" => "Sick",
            "status_id" => $status->id,
            "language_name" => "en",
        ]);
        StatusTran::factory()->create([
            "value" => "بیمار",
            "status_id" => $status->id,
            "language_name" => "fa",
        ]);
        StatusTran::factory()->create([
            "value" => "ناروغه",
            "status_id" => $status->id,
            "language_name" => "ps",
        ]);
    }

    public function nidTypes()
    {
        $nid = NidType::create([]);
        NidTypeTrans::create([
            "value" => "پاسپورت",
            "language_name" => "fa",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "پاسپورټ",
            "language_name" => "ps",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "Passport",
            "language_name" => "en",
            "nid_type_id" => $nid->id
        ]);
        $nid = NidType::create([]);
        NidTypeTrans::create([
            "value" => "تذکره الکترونیکی",
            "language_name" => "fa",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "برېښنايي پېژندپاڼه",
            "language_name" => "ps",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "Electronic ID card",
            "language_name" => "en",
            "nid_type_id" => $nid->id
        ]);
        $nid = NidType::create([]);
        NidTypeTrans::create([
            "value" => "تذکره کاغذی",
            "language_name" => "fa",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "د کاغذ پېژندپاڼه",
            "language_name" => "ps",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "Paper ID card",
            "language_name" => "en",
            "nid_type_id" => $nid->id
        ]);
    }
    public function shifts()
    {
        $shift = Shift::factory()->create([
            'start_time' => Carbon::today()->setTime(8, 0)->toTimeString(),  // 8:00 AM
            'end_time' => Carbon::today()->setTime(4, 0)->toTimeString(),    // 9:00 AM
            "description" => "",
        ]);
        ShiftTran::factory()->create([
            "value" => "8 to 4 Shift",
            "shift_id" => $shift->id,
            "language_name" => "en",
        ]);
        ShiftTran::factory()->create([
            "value" => "شیفت ۸ تا ۴",
            "shift_id" => $shift->id,
            "language_name" => "fa",
        ]);
        ShiftTran::factory()->create([
            "value" => "له ۸ څخه تر ۴ پورې شفټ",
            "shift_id" => $shift->id,
            "language_name" => "ps",
        ]);
    }

    public function maritalStatus()
    {
        $statuses = [
            'Single' => ['ps' => 'مجرد', 'fa' => 'مجرد'],
            'Married' => ['ps' => 'واده شوی', 'fa' => 'متاهل'],
            'Widowed' => ['ps' => 'کونډه', 'fa' => 'بیوه'],
        ];

        foreach ($statuses as $english => $translations) {
            $marital = MaritalStatus::create(); // assuming no fields required

            // English
            MaritalStatusTran::create([
                'marital_status_id' => $marital->id,
                'value' => $english,
                'language_name' => 'en',
            ]);

            // Pashto
            MaritalStatusTran::create([
                'marital_status_id' => $marital->id,
                'value' => $translations['ps'],
                'language_name' => 'ps',
            ]);

            // Farsi
            MaritalStatusTran::create([
                'marital_status_id' => $marital->id,
                'value' => $translations['fa'],
                'language_name' => 'fa',
            ]);
        }
    }

    public function currency()
    {


        $currencies = [
            [
                'abbr' => 'AFN',
                'symbol' => '؋',
                'translations' => [
                    'en' => 'Afghani',
                    'ps' => 'افغانی',
                    'fa' => 'افغانی',
                ],
            ],
            [
                'abbr' => 'USD',
                'symbol' => '$',
                'translations' => [
                    'en' => 'US Dollar',
                    'ps' => 'ډالر',
                    'fa' => 'دالر',
                ],
            ],
            [
                'abbr' => 'EUR',
                'symbol' => '€',
                'translations' => [
                    'en' => 'Euro',
                    'ps' => 'یورو',
                    'fa' => 'یورو',
                ],
            ],
        ];

        foreach ($currencies as $currency) {
            $curr = Currency::create([
                'abbr' => $currency['abbr'],
                'symbol' => $currency['symbol'],
            ]);

            foreach ($currency['translations'] as $lang => $value) {
                CurrencyTran::create([
                    'currency_id' => $curr->id,
                    'value' => $value,
                    'language_name' => $lang,
                ]);
            }
        }
    }

    protected function gender()
    {

        Gender::create([
            'name_en' => 'Male',
            'name_fa' => 'مرد',
            'name_ps' => 'نارینه'
        ]);

        Gender::create([
            'name_en' => 'Famale',
            'name_fa' => 'زن',
            'name_ps' => 'ښځینه'
        ]);
    }
    public function languages(): void
    {
        Language::factory()->create([
            "name" => "en"
        ]);
        Language::factory()->create([
            "name" => "ps"
        ]);
        Language::factory()->create([
            "name" => "fa"
        ]);
    }
}
