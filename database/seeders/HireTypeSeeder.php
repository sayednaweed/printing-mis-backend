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
        $this->hireType();
    }

    protected function hireType()
    {
        $hireTypes = [
            [
                'description' => '',
                'translations' => [
                    'en' => 'Contractual',
                    'ps' => 'قراردادي',
                    'fa' => 'قراردادی',
                ]
            ],
            [
                'description' => '',
                'translations' => [
                    'en' => 'Permanent',
                    'ps' => 'دائمی',
                    'fa' => 'دایمي',
                ]
            ],
            [
                'description' => '',
                'translations' => [
                    'en' => 'Temporary',
                    'ps' => 'موقتی',
                    'fa' => 'لنډمهاله',
                ]
            ],
            [
                'description' => '',
                'translations' => [
                    'en' => 'Internship',
                    'ps' => 'موقتی',
                    'fa' => 'کارآموزی',
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
