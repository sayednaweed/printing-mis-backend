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
                'detail' => '',
                'translations' => [
                    'en' => 'Contractual',
                    'ps' => 'قراردادي',
                    'fa' => 'قراردادی',
                ]
            ],
            [
                'detail' => '',
                'translations' => [
                    'en' => 'Permanent',
                    'ps' => 'دائمی',
                    'fa' => 'دایمي',
                ]
            ],
            [
                'detail' => '',
                'translations' => [
                    'en' => 'Temporary',
                    'ps' => 'موقتی',
                    'fa' => 'لنډمهاله',
                ]
            ],
            [
                'detail' => '',
                'translations' => [
                    'en' => 'Internship',
                    'ps' => 'موقتی',
                    'fa' => 'کارآموزی',
                ]
            ]
        ];

        foreach ($hireTypes as $typeData) {
            $hireType = HireType::create([
                'detail' => $typeData['detail'],
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
                'en' => 'Promotion',
                'fa' => 'ارتقا', // Promotion in Farsi
                'ps' => 'ارتقا'  // Promotion in Pashto
            ],
            'demotion' => [
                'en' => 'Demotion',
                'fa' => 'تنزل', // Demotion in Farsi
                'ps' => 'تنزل'  // Demotion in Pashto
            ],
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
