<?php

namespace Database\Seeders;

use App\Models\Icon;
use App\Models\IconTran;
use App\Models\ExpenseType;
use App\Models\ExpenseStatus;
use App\Models\ExpenseTypeIcon;
use App\Models\ExpenseTypeTran;
use Illuminate\Database\Seeder;
use App\Models\ExpenseStatusTrans;
use App\Enums\Status\ExpenseStatusEnum;
use App\Enums\Status\TransactionStatusEnum;
use App\Models\TransactionStatus;
use App\Models\TransactionStatusTrans;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->expenseStatus();
        $this->expenseAndIcons();
    }

    private function expenseStatus()
    {
        $item = TransactionStatus::factory()->create([
            'id' => TransactionStatusEnum::pending->value
        ]);

        TransactionStatusTrans::factory()->create([
            'value' => 'Pending',
            'transaction_status_id' => $item->id,
            'language_name' => 'en',
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'در انتظار',
            'transaction_status_id' => $item->id,
            'language_name' => 'fa',
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'په انتظار',
            'transaction_status_id' => $item->id,
            'language_name' => 'ps',
        ]);
        $item = TransactionStatus::factory()->create([
            'id' => TransactionStatusEnum::completed->value
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'Completed',
            'transaction_status_id' => $item->id,
            'language_name' => 'en',
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'تکمیل شده',
            'transaction_status_id' => $item->id,
            'language_name' => 'fa',
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'بشپړ شوی',
            'transaction_status_id' => $item->id,
            'language_name' => 'ps',
        ]);
        $item = TransactionStatus::factory()->create([
            'id' => TransactionStatusEnum::cancelled->value
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'Cancelled',
            'transaction_status_id' => $item->id,
            'language_name' => 'en',
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'لغو شده',
            'transaction_status_id' => $item->id,
            'language_name' => 'fa',
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'لغوه شوی',
            'transaction_status_id' => $item->id,
            'language_name' => 'ps',
        ]);
        $item = TransactionStatus::factory()->create([
            'id' => TransactionStatusEnum::refunded->value
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'Refunded',
            'transaction_status_id' => $item->id,
            'language_name' => 'en',
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'مسترد شده',
            'transaction_status_id' => $item->id,
            'language_name' => 'fa',
        ]);
        TransactionStatusTrans::factory()->create([
            'value' => 'بېرته ورکړل شوی',
            'transaction_status_id' => $item->id,
            'language_name' => 'ps',
        ]);
    }

    private function expenseAndIcons()
    {
        $items = [
            [
                [
                    'path' => 'icons/expense-icons/glass.svg',
                    'icon' => [
                        'en' => 'Glass',
                        'fa' => 'گیلاس',
                        'ps' => 'گیلاس',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/clock.svg',
                    'icon' => [
                        'en' => 'Clock',
                        'fa' => 'ساعت',
                        'ps' => 'ساعت',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/tea.svg',
                    'icon' => [
                        'en' => 'Tea',
                        'fa' => 'چای',
                        'ps' => 'چای',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/umbrella.svg',
                    'icon' => [
                        'en' => 'Umbrella',
                        'fa' => 'چتری',
                        'ps' => 'چترۍ',
                    ],
                ],
                'expense_type' => [
                    'en' => 'Household Items',
                    'fa' => 'لوازم منزل',
                    'ps' => 'د پخلنځي وسایل',
                ]
            ],
            [
                [
                    'path' => 'icons/expense-icons/battery.svg',
                    'icon' => [
                        'en' => 'Battery',
                        'fa' => 'باتری',
                        'ps' => 'بېټري',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/parts.svg',
                    'icon' => [
                        'en' => 'Parts',
                        'fa' => 'قطعات',
                        'ps' => 'پرزې',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/phone.svg',
                    'icon' => [
                        'en' => 'Phone',
                        'fa' => 'تلفن',
                        'ps' => 'تلفن',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/headphones.svg',
                    'icon' => [
                        'en' => 'Headphones',
                        'fa' => 'هدفون',
                        'ps' => 'هدفون',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/monitor.svg',
                    'icon' => [
                        'en' => 'Monitor',
                        'fa' => 'مانیتور',
                        'ps' => 'مانیتور',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/camera.svg',
                    'icon' => [
                        'en' => 'Camera',
                        'fa' => 'کامره',
                        'ps' => 'کیمره',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/plane.svg',
                    'icon' => [
                        'en' => 'Plane',
                        'fa' => 'طیاره',
                        'ps' => 'الوتکه',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/router.svg',
                    'icon' => [
                        'en' => 'Router',
                        'fa' => 'روتر',
                        'ps' => 'روتر',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/bulb.svg',
                    'icon' => [
                        'en' => 'Bulb',
                        'fa' => 'گروپ',
                        'ps' => 'گروپ',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/printer.svg',
                    'icon' => [
                        'en' => 'Printer',
                        'fa' => 'پرنتر',
                        'ps' => 'پرنتر',
                    ],
                ],
                'expense_type' => [
                    'en' => 'Electronics',
                    'fa' => 'الکترونیک',
                    'ps' => 'الکترونیک',
                ]
            ],
            [
                [
                    'path' => 'icons/expense-icons/book.svg',
                    'icon' => [
                        'en' => 'Book',
                        'fa' => 'کتاب',
                        'ps' => 'کتاب',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/paper.svg',
                    'icon' => [
                        'en' => 'Paper',
                        'fa' => 'کاغذ',
                        'ps' => 'کاغذ',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/pencil.svg',
                    'icon' => [
                        'en' => 'Pencil',
                        'fa' => 'پنسل',
                        'ps' => 'پنسل',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/magnifying.svg',
                    'icon' => [
                        'en' => 'Magnifier',
                        'fa' => 'ذره بین',
                        'ps' => 'ذره بین',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/brush.svg',
                    'icon' => [
                        'en' => 'Brush',
                        'fa' => 'برش',
                        'ps' => 'برش',
                    ],
                ],
                'expense_type' => [
                    'en' => 'Stationery',
                    'fa' => 'قرطاسیه',
                    'ps' => 'قرطاسیه',
                ]
            ],
            [
                [
                    'path' => 'icons/expense-icons/briefcase.svg',
                    'icon' => [
                        'en' => 'Briefcase',
                        'fa' => 'بکس‌',
                        'ps' => 'بکس‌',
                    ],
                ],
                [
                    'path' => 'icons/expense-icons/lock.svg',
                    'icon' => [
                        'en' => 'Lock',
                        'fa' => 'قفل',
                        'ps' => 'قفل',
                    ],
                ],
                'expense_type' => [
                    'en' => 'Accessories',
                    'fa' => 'لوازمات',
                    'ps' => 'لوازمات',
                ]
            ],
            [
                [
                    'path' => 'icons/expense-icons/car.svg',
                    'icon' => [
                        'en' => 'Car',
                        'fa' => 'موتر',
                        'ps' => 'موټر',
                    ],
                ],
                'expense_type' => [
                    'en' => 'Machine',
                    'fa' => 'ماشین',
                    'ps' => 'ماشین',
                ]
            ],
            [
                [
                    'path' => 'icons/expense-icons/wallet.svg',
                    'icon' => [
                        'en' => 'Fare',
                        'fa' => 'کرایه',
                        'ps' => 'کرایه',
                    ],
                ],
                'expense_type' => [
                    'en' => 'Transportation',
                    'fa' => 'ترانسپورت',
                    'ps' => 'ترانسپورت',
                ]
            ],
            [
                [
                    'path' => 'icons/expense-icons/building.svg',
                    'icon' => [
                        'en' => 'Building',
                        'fa' => 'ساختمان',
                        'ps' => 'ودانۍ',
                    ],
                ],
                'expense_type' => [
                    'en' => 'Properties',
                    'fa' => 'املاک',
                    'ps' => 'جایداد',
                ]
            ],
            [
                [
                    'path' => 'icons/expense-icons/shirt.svg',
                    'icon' => [
                        'en' => 'Shirt',
                        'fa' => 'پیراهن',
                        'ps' => 'پیراهن',
                    ],
                ],
                'expense_type' => [
                    'en' => 'Clothing',
                    'fa' => 'لباس',
                    'ps' => 'جامې',
                ]
            ],
        ];




        foreach ($items as $group) {
            // Extract and remove the expense_type from the group
            $expenseTypeData = $group['expense_type'];
            unset($group['expense_type']);

            // Create the expense type record without icon_id first (will be updated after icons are created)
            $expenseType = ExpenseType::create();

            foreach ($group as $iconData) {
                // Create the icon
                $icon = Icon::create([
                    'path' => $iconData['path'],
                    'type' => 'image/svg+xml',
                    'extension' => '.svg'
                ]);

                // Create icon translations
                foreach ($iconData['icon'] as $lang => $value) {
                    IconTran::create([
                        'language_name' => $lang,
                        'icon_id' => $icon->id,
                        'value' => $value,
                    ]);
                }
                ExpenseTypeIcon::create([
                    'icon_id' => $icon->id,
                    'expense_type_id' => $expenseType->id,
                ]);
            }

            // Create expense type translations
            foreach ($expenseTypeData as $lang => $value) {
                ExpenseTypeTran::create([
                    'language_name' => $lang,
                    'expense_type_id' => $expenseType->id,
                    'value' => $value,
                ]);
            }
        }
    }
}
