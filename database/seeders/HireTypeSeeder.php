<?php

namespace Database\Seeders;

use App\Models\HireType;
use App\Models\HireTypeTran;
use App\Models\PositionChangeType;
use App\Models\PositionChangeTypeTran;
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
        $this->positionChangeType();
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

    protected function  positionChangeType()
    {
        // First, create an array of position change types and their corresponding translations
        $positionChangeTypes = [
            'promotion' => [
                'en' => 'promotion',
                'fa' => 'ترفیع', // Promotion in Farsi
                'ps' => 'ترفیع'  // Promotion in Pashto
            ],
            'demotion' => [
                'en' => 'demotion',
                'fa' => 'تنزل رتبه', // Demotion in Farsi
                'ps' => 'کموالی'  // Demotion in Pashto
            ],
            'change of grade' => [
                'en' => 'change of grade',
                'fa' => 'تغییر درجه', // Change of Grade in Farsi
                'ps' => 'د درجه بدلون' // Change of Grade in Pashto
            ],
            'change of position' => [
                'en' => 'change of position',
                'fa' => 'تغییر پست', // Change of Position in Farsi
                'ps' => 'د پوست بدلون' // Change of Position in Pashto
            ]
        ];

        // Loop through the array to create PositionChangeTypes and their translations
        foreach ($positionChangeTypes as $type => $translations) {
            // Create the PositionChangeType
            $post = PositionChangeType::create();

            // Loop through the translations array and create the PositionChangeTypeTran for each language
            foreach ($translations as $language => $value) {
                PositionChangeTypeTran::create([
                    'position_change_type_id' => $post->id,
                    'language_name' => $language,
                    'value' => $value
                ]);
            }
        }
    }
}
