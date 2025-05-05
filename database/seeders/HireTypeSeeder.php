<?php

namespace Database\Seeders;

use App\Models\HireType;
use App\Models\HireTypeTran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HireTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }


    protected function hireType()
    {
        $hireTypes = [
            [
                'description' => 'Full Time',
                'translations' => [
                    'en' => 'Full Time',
                    'ps' => 'د بشپړ وخت',
                    'fa' => 'تمام وقت',
                ]
            ],
            [
                'description' => 'Part Time',
                'translations' => [
                    'en' => 'Part Time',
                    'ps' => 'د نیمه وخت',
                    'fa' => 'نیمه وقت',
                ]
            ],
            [
                'description' => 'Contract',
                'translations' => [
                    'en' => 'Contract',
                    'ps' => 'قراردادي',
                    'fa' => 'قراردادی',
                ]
            ]
        ];

        foreach ($hireTypes as $typeData) {
            $hireType = HireType::create([
                'description' => $typeData['description'],
            ]);

            foreach ($typeData['translations'] as $lang => $value) {
                HireTypeTran::create([
                    'hire_type_id' => $hireType->id,
                    'language_name' => $lang,
                    'value' => $value,
                ]);
            }
        }
    }
}
