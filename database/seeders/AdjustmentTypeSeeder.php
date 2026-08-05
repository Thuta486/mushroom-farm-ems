<?php

namespace Database\Seeders;

use App\Models\AdjustmentType;
use Illuminate\Database\Seeder;

class AdjustmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $adjustmentTypes = [
            [
                'name_en' => 'Performance Bonus',
                'name_my' => 'စွမ်းဆောင်ရည်ဆုကြေး',
                'category' => 'bonus',
            ],
            [
                'name_en' => 'Attendance Bonus',
                'name_my' => 'တက်ရောက်မှုဆုကြေး',
                'category' => 'bonus',
            ],
            [
                'name_en' => 'Equipment Damage',
                'name_my' => 'ပစ္စည်းပျက်စီးမှုဖြတ်တောက်မှု',
                'category' => 'deduction',
            ],
            [
                'name_en' => 'Other Deduction',
                'name_my' => 'အခြားနုတ်ယူငွေ',
                'category' => 'deduction',
            ],

        ];

        foreach ($adjustmentTypes as $type) {
            AdjustmentType::query()->updateOrCreate(
                ['name_en' => $type['name_en']],
                [
                    'name_my' => $type['name_my'],
                    'category' => $type['category'],
                ],
            );
        }
    }
}
