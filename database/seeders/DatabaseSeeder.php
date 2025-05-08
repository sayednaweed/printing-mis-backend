<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Shift;
use App\Models\Gender;
use App\Models\NidType;
use App\Models\Currency;
use App\Models\Language;
use App\Models\ShiftTran;
use App\Models\CurrencyTran;
use App\Models\NidTypeTrans;
use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;
use App\Models\MaritalStatusTran;
use Database\Seeders\CheckListSeeder;
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
            "value" => "تذکره",
            "language_name" => "fa",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "تذکره",
            "language_name" => "ps",
            "nid_type_id" => $nid->id
        ]);
        NidTypeTrans::create([
            "value" => "ID card",
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
