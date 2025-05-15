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
                'path' => 'icons/.svg',
                'translations' => [
                    'en' => 'Car',
                    'fa' => 'موتر',
                    'ps' => 'موټر',
                ],
                'expense_translations' => [
                    'en' => 'Vehicle Expense',
                    'fa' => 'مصرف وسایط',
                    'ps' => 'د موټر مصرف',
                ]
            ],
            [
                'path' => 'icons/food.svg',
                'translations' => [
                    'en' => 'Food',
                    'fa' => 'غذا',
                    'ps' => 'خواړه',
                ],
                'expense_translations' => [
                    'en' => 'Food Expense',
                    'fa' => 'مصرف غذا',
                    'ps' => 'د خوړو مصرف',
                ]
            ],
            [
                'path' => 'icons/electricity.svg',
                'translations' => [
                    'en' => 'Electricity',
                    'fa' => 'برق',
                    'ps' => 'برېښنا',
                ],
                'expense_translations' => [
                    'en' => 'Electricity Bill',
                    'fa' => 'مصرف برق',
                    'ps' => 'د برق مصرف',
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
    //         'path' => 'icons/'.'',
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
