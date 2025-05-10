<?php

namespace Database\Seeders;

use App\Enums\Attendance\AttendanceStatusEnum;
use App\Models\AttendanceStatus;
use App\Models\AttendanceStatusTran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->attendanceStatus();
    }


    private function attendanceStatus()
    {
        $statuses = [
            AttendanceStatusEnum::present->value => [
                'en' => 'Present',
                'fa' => 'حاضر',
                'ps' => 'حاضر',
            ],
            AttendanceStatusEnum::absent->value => [  // Fixed typo: 'absend' to 'absent'
                'en' => 'Absent',
                'fa' => 'غایب',
                'ps' => 'ناشته',
            ],
            AttendanceStatusEnum::leave->value => [
                'en' => 'Leave',
                'fa' => 'رخصت',
                'ps' => 'رخصتی',
            ],
            AttendanceStatusEnum::sick->value => [
                'en' => 'Sick',
                'fa' => 'بیمار',
                'ps' => 'بیمار',
            ],
            // Add more statuses here...
        ];

        foreach ($statuses as $enum => $translations) {
            // Create the attendance status
            $att = AttendanceStatus::create([
                'id' => $enum
            ]);

            // Create the translations for each language
            foreach ($translations as $lang => $value) {
                AttendanceStatusTran::create([
                    'attendance_status_id' => $att->id,
                    'language_name' => $lang,
                    'value' => $value
                ]);
            }
        }
    }
}
