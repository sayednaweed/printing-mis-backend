<?php

namespace Database\Seeders;

use App\Models\ExpenseType;
use App\Models\ExpenseTypeTran;
use App\Models\Icon;
use App\Models\IconTran;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IconAndExpenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $this->expenseAndIcons();
    }

    private function expenseAndIcons()
    {
        $items = [

            [
                'path' => 'icons/expense-icons/electricity.svg',
                'translations' => [
                    'en' => 'electricity',
                    'fa' => 'برق',
                    'ps' => 'برېښنا',
                ],
                'expense_translations' => [
                    'en' => 'electricity expense',
                    'fa' => 'مصرف برق',
                    'ps' => 'د برق مصرف',
                ]
            ],
            [
                'path' => 'icons/expense-icons/device.svg',
                'translations' => [
                    'en' => 'device',
                    'fa' => 'دستگاه ',
                    'ps' => 'دستگاه ',
                ],
                'expense_translations' => [
                    'en' => 'device expense',
                    'fa' => 'مصرف برق',
                    'ps' => 'د برق مصرف',
                ]
            ],  
            [
                'path' => 'icons/expense-icons/food.svg',
                'translations' => [
                    'en' => 'food',
                    'fa' => 'غذا ',
                    'ps' => 'خواړه ',
                ],
                'expense_translations' => [
                    'en' => 'food expense',
                    'fa' => 'مصرف غذا',
                    'ps' => 'د خواړه مصرف',
                ]
            ], 
              [
                'path' => 'icons/expense-icons/drink.svg',
                'translations' => [
                    'en' => 'drink',
                    'fa' => 'نوشیدنی ',
                    'ps' => 'خواړه ',
                ],
                'expense_translations' => [
                    'en' => 'drink expense',
                    'fa' => 'مصرف نوشیدنی',
                    'ps' => 'د څښاک مصرف',
                ]
            ],  
            [
                'path' => 'icons/expense-icons/stationery.svg',
                'translations' => [
                    'en' => 'stationery',
                    'fa' => 'قرطاسیه ',
                    'ps' => 'قرطاسیه ',
                ],
                'expense_translations' => [
                    'en' => 'stationery expense',
                    'fa' => 'مصرف قرطاسیه',
                    'ps' => 'د قرطاسیه مصرف',
                ]
            ],  
            [
                'path' => 'icons/expense-icons/stationery.svg',
                'translations' => [
                    'en' => 'stationery',
                    'fa' => 'قرطاسیه ',
                    'ps' => 'قرطاسیه ',
                ],
                'expense_translations' => [
                    'en' => 'stationery expense',
                    'fa' => 'مصرف قرطاسیه',
                    'ps' => 'د قرطاسیه مصرف',
                ]
            ], 
            [
                'path' => 'icons/expense-icons/oil.svg',
                'translations' => [
                    'en' => 'oil',
                    'fa' => 'روغن ',
                    'ps' => 'غوړي ',
                ],
                'expense_translations' => [
                    'en' => 'oil expense',
                    'fa' => 'مصرف روغن',
                    'ps' => 'د غوړي مصرف',
                ]
            ], 
            [
                'path' => 'icons/expense-icons/transport.svg',
                'translations' => [
                    'en' => 'transport',
                    'fa' => 'ترانسپورت',
                    'ps' => 'ټرانسپورټ ',
                ],
                'expense_translations' => [
                    'en' => 'transport expense',
                    'fa' => 'مصرف ترانسپورت',
                    'ps' => 'د ټرانسپورټ مصرف',
                ]
            ], 
            [
                'path' => 'icons/expense-icons/transport.svg',
                'translations' => [
                    'en' => 'transport',
                    'fa' => 'ترانسپورت',
                    'ps' => 'ټرانسپورټ ',
                ],
                'expense_translations' => [
                    'en' => 'transport expense',
                    'fa' => 'مصرف ترانسپورت',
                    'ps' => 'د ټرانسپورټ مصرف',
                ]
            ], 
            [
                'path' => 'icons/expense-icons/maintenance.svg',
                'translations' => [
                    'en' => 'maintenance',
                    'fa' => 'نگهداری',
                    'ps' => 'ساتنه ',
                ],
                'expense_translations' => [
                    'en' => 'maintenance expense',
                    'fa' => 'مصرف نگهداری',
                    'ps' => 'د ساتنه مصرف',
                ]
            ], 
            [
                'path' => 'icons/expense-icons/maintenance.svg',
                'translations' => [
                    'en' => 'maintenance',
                    'fa' => 'نگهداری',
                    'ps' => 'ساتنه ',
                ],
                'expense_translations' => [
                    'en' => 'maintenance expense',
                    'fa' => 'مصرف نگهداری',
                    'ps' => 'د ساتنه مصرف',
                ]
            ], 
            [
                'path' => 'icons/expense-icons/rent.svg',
                'translations' => [
                    'en' => 'rent',
                    'fa' => 'کرایه',
                    'ps' => 'کرایه ',
                ],
                'expense_translations' => [
                    'en' => 'rent expense',
                    'fa' => 'مصرف کرایه',
                    'ps' => 'د کرایه مصرف',
                ] 
            ], 


           
        ];

        foreach ($items as $item) {
            // Create icon
            $icon = Icon::create(['path' => $item['path']]);

            // Create icon translations
            foreach ($item['translations'] as $lang => $value) {
                IconTran::create([
                    'language_name' => $lang,
                    'icon_id' => $icon->id,
                    'value' => $value,
                ]);
            }

            // Create expense type with the icon
            $expenseType = ExpenseType::create(['icon_id' => $icon->id]);

            // Create expense type translations
            foreach ($item['expense_translations'] as $lang => $value) {
                ExpenseTypeTran::create([
                    'language_name' => $lang,
                    'expense_type_id' => $expenseType->id,
                    'value' => $value,
                ]);
            }
        }
    }

    //  {

    //     $icon =   Icon::create([
    //         'path' => 'icons/expense-icons/'.'',
    //     ]);
    //     IconTran::create([
    //         'language_name' => 'en',
    //         'icon_id' =>$icon->id,
    //         'value' => ''
    //     ]);
    //     IconTran::create([
    //         'language_name' => 'fa',
    //         'icon_id' =>$icon->id,
    //         'value' => ''
    //     ]);
    //     IconTran::create([
    //         'language_name' => 'ps',
    //         'icon_id' =>$icon->id,
    //         'value' => ''
    //     ]);
    //     $exp=     ExpenseType::create([
    //         'icon_id' => $icon->id
    //     ]);
    //     ExpenseTypeTran::create([
    //         'language_name' => 'en',
    //         'expense_type_id' =>$exp->id,
    //         'value' => ''
    //     ]);
    //     ExpenseTypeTran::create([
    //         'language_name' => 'ps',
    //         'expense_type_id' =>$exp->id,
    //         'value' => ''
    //     ]);
    //     ExpenseTypeTran::create([
    //         'language_name' => 'fa',
    //         'expense_type_id' =>$exp->id,
    //         'value' => ''
    //     ]);

    // }
}
