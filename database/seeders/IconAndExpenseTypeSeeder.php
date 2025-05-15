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
                'path' => 'icons/air-conditioner.svg',
                'translations' => [
                    'en' => 'conditioner',
                    'fa' => 'برق',
                    'ps' => 'برېښنا',
                ],
                'expense_translations' => [
                    'en' => 'conditioner expense',
                    'fa' => 'مصرف برق',
                    'ps' => 'د برق مصرف',
                ]
            ],
            [
                'path' => 'icons/battery.svg',
                'translations' => [
                    'en' => 'battery',
                    'fa' => 'بطری',
                    'ps' => 'بطری',
                ],
                'expense_translations' => [
                    'en' => 'bettery expense',
                    'fa' => 'مصرف بطری',
                    'ps' => ' بطری مصرف',
                ]
            ],
            [
                'path' => 'icons/bill.svg',
                'translations' => [
                    'en' => 'bill',
                    'fa' => 'بل',
                    'ps' => 'بل',
                ],
                'expense_translations' => [
                    'en' => 'bettery expense',
                    'fa' => 'مصرف بل',
                    'ps' => ' بل مصرف',
                ]
            ],
            [
                'path' => 'icons/bread.svg',
                'translations' => [
                    'en' => 'bread',
                    'fa' => 'نان',
                    'ps' => 'ډوډي',
                ],
                'expense_translations' => [
                    'en' => 'bread expense',
                    'fa' => 'مصرف نان',
                    'ps' => ' ډوډي مصرف',
                ]
            ],
            [
                'path' => 'icons/calendar.svg',
                'translations' => [
                    'en' => 'calendar',
                    'fa' => 'تقویم',
                    'ps' => 'کلیزه',
                ],
                'expense_translations' => [
                    'en' => 'bread expense',
                    'fa' => 'مصرف تقویم',
                    'ps' => ' کلیزه مصرف',
                ]
            ],
            [
                'path' => 'icons/camera.svg',
                'translations' => [
                    'en' => 'camera',
                    'fa' => 'کمره',
                    'ps' => 'کامره',
                ],
                'expense_translations' => [
                    'en' => 'camera expense',
                    'fa' => 'مصرف کمره',
                    'ps' => ' کامره مصرف',
                ]
            ],
            [
                'path' => 'icons/cardholder.svg',
                'translations' => [
                    'en' => 'cardholder',
                    'fa' => 'پوش کارت',
                    'ps' => 'د کارت ځای',
                ],
                'expense_translations' => [
                    'en' => 'cardholder expense',
                    'fa' => 'مصرف پوش کارت',
                    'ps' => ' د کارت پوښ مصرف',
                ]
            ],
            [
                'path' => 'icons/chair.svg',
                'translations' => [
                    'en' => 'chair',
                    'fa' => 'چوکی ',
                    'ps' => ' څوکی ',
                ],
                'expense_translations' => [
                    'en' => 'chair expense',
                    'fa' => 'مصرف چوکی ',
                    'ps' => 'څوکی مصرف',
                ]
            ],
            [
                'path' => 'icons/clock.svg',
                'translations' => [
                    'en' => 'clock',
                    'fa' => 'ساعت ',
                    'ps' => ' ساعت ',
                ],
                'expense_translations' => [
                    'en' => 'clock expense',
                    'fa' => 'مصرف ساعت ',
                    'ps' => 'ساعت مصرف',
                ]
            ],
            [
                'path' => 'icons/closet.svg',
                'translations' => [
                    'en' => 'closet',
                    'fa' => 'الماری ',
                    'ps' => ' الماري ',
                ],
                'expense_translations' => [
                    'en' => 'closet expense',
                    'fa' => 'مصرف الماری ',
                    'ps' => 'الماري مصرف',
                ]
            ],
            [
                'path' => 'icons/monitor.svg',
                'translations' => [
                    'en' => 'monitor',
                    'fa' => 'مانیتور ',
                    'ps' => ' مانيټر ',
                ],
                'expense_translations' => [
                    'en' => 'monitor expense',
                    'fa' => 'مصرف مانیتور ',
                    'ps' => 'مانيټر مصرف',
                ]
            ],
            [
                'path' => 'icons/drink.svg',
                'translations' => [
                    'en' => 'drink',
                    'fa' => 'نوشیدنی  ',
                    'ps' => ' څښاک  ',
                ],
                'expense_translations' => [
                    'en' => 'drink expense',
                    'fa' => 'مصرف نوشیدنی ',
                    'ps' => 'څښاک مصرف ',
                ]
            ],
            [
                'path' => 'icons/flash-drive.svg',
                'translations' => [
                    'en' => 'flash-drive',
                    'fa' => 'فلش درایف',
                    'ps' => 'فلش درایف',
                ],
                'expense_translations' => [
                    'en' => 'flash-drive expense',
                    'fa' => 'مصرف فلش درایق',
                    'ps' => 'فلش درایف مصرف',
                ]
            ],
            [
                'path' => 'icons/fuel.svg',
                'translations' => [
                    'en' => 'fuel',
                    'fa' => 'تیل ',
                    'ps' => 'تیل',
                ],
                'expense_translations' => [
                    'en' => 'fuel expense',
                    'fa' => 'مصرف تیل',
                    'ps' => 'تیل مصرف',
                ]
            ],
            [
                'path' => 'icons/generator.svg',
                'translations' => [
                    'en' => 'generator',
                    'fa' => 'جنراتور ',
                    'ps' => 'جنراتور',
                ],
                'expense_translations' => [
                    'en' => 'generator expense',
                    'fa' => 'مصرف جنراتور',
                    'ps' => ' جنراتور مصرف ',
                ]
            ],
            [
                'path' => 'icons/wifi.svg',
                'translations' => [
                    'en' => 'wifi',
                    'fa' => 'وایفای ',
                    'ps' => 'وایفای',
                ],
                'expense_translations' => [
                    'en' => 'wifi expense',
                    'fa' => 'مصرف وایفای',
                    'ps' => 'وایفای مصرف ',
                ]
            ],
            [
                'path' => 'icons/hosting.svg',
                'translations' => [
                    'en' => 'hosting',
                    'fa' => 'هاستینگ ',
                    'ps' => 'هاستینگ',
                ],
                'expense_translations' => [
                    'en' => 'hosting expense',
                    'fa' => 'مصرف هاستینگ',
                    'ps' => 'هاستینگ مصرف ',
                ]
            ],
            [
                'path' => 'icons/internet.svg',
                'translations' => [
                    'en' => 'internet',
                    'fa' => 'انترنیت ',
                    'ps' => 'انترنیت',
                ],
                'expense_translations' => [
                    'en' => 'internet expense',
                    'fa' => 'مصرف انترنیت',
                    'ps' => 'انترنیت مصرف ',
                ]
            ],
            [
                'path' => 'icons/internet-cable.svg',
                'translations' => [
                    'en' => 'internet-cable',
                    'fa' => 'کیبل انترنیت  ',
                    'ps' => 'انترنیت کیبل',
                ],
                'expense_translations' => [
                    'en' => 'internet-cable expense',
                    'fa' => 'مصرف کیبل انترنیت',
                    'ps' => 'انترنیت کیبل مصرف ',
                ]
            ],
            [
                'path' => 'icons/keyboard.svg',
                'translations' => [
                    'en' => 'keyboard',
                    'fa' => 'کیبورد',
                    'ps' => 'کیبورد',
                ],
                'expense_translations' => [
                    'en' => 'keyboard expense',
                    'fa' => 'مصرف کیبورد ',
                    'ps' => 'کیبورد مصرف ',
                ]
            ],
            [
                'path' => 'icons/laptop.svg',
                'translations' => [
                    'en' => 'laptop',
                    'fa' => 'لبتاپ',
                    'ps' => 'لبتاپ',
                ],
                'expense_translations' => [
                    'en' => 'laptop expense',
                    'fa' => 'مصرف لبتاپ ',
                    'ps' => ' لبتاپ مصرف',
                ]
            ],
            [
                'path' => 'icons/light.svg',
                'translations' => [
                    'en' => 'light',
                    'fa' => 'گروپ',
                    'ps' => 'گروپ',
                ],
                'expense_translations' => [
                    'en' => 'light expense',
                    'fa' => 'مصرف گروپ ',
                    'ps' => ' گروپ مصرف',
                ]
            ],
            [
                'path' => 'icons/maintenance.svg',
                'translations' => [
                    'en' => 'maintenance',
                    'fa' => 'نگهداری ',
                    'ps' => 'ساتنه ',
                ],
                'expense_translations' => [
                    'en' => 'light expense',
                    'fa' => 'مصرف نگهداری',
                    'ps' => ' ساتنی مصرف',
                ]
            ],
            [
                'path' => 'icons/medical-kit.svg',
                'translations' => [
                    'en' => 'medical kit',
                    'fa' => 'کمک های اولیه',
                    'ps' => 'لومړنۍ مرستې ',
                ],
                'expense_translations' => [
                    'en' => 'medical kit expense',
                    'fa' => 'مصرف کمک های اولیه',
                    'ps' => ' لومړنۍ مرستې مصرف',
                ]
            ],
            [
                'path' => 'icons/microphone.svg',
                'translations' => [
                    'en' => 'microphone',
                    'fa' => 'میکروفون ',
                    'ps' => 'میکروفون ',
                ],
                'expense_translations' => [
                    'en' => 'medical kit expense',
                    'fa' => 'مصرف کمک های اولیه',
                    'ps' => ' لومړنۍ مرستې مصرف',
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
