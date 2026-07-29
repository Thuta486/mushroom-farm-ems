<?php

namespace Database\Seeders;

use App\Models\AdjustmentType;
use App\Models\AdvanceType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $adjustmentTypes = [
            ['name' => 'Performance Bonus', 'category' => 'bonus'],
            ['name' => 'Festival Bonus', 'category' => 'bonus'],
            ['name' => 'Tool Damage', 'category' => 'deduction'],
            ['name' => 'Late Arrival', 'category' => 'deduction'],
            ['name' => 'Other Deduction', 'category' => 'deduction'],
        ];

        foreach ($adjustmentTypes as $type) {
            AdjustmentType::query()->firstOrCreate(
                ['name' => $type['name']],
                ['category' => $type['category']],
            );
        }

        $advanceTypes = [
            'Cash Advance',
            'Mushroom Eating Cash',
            'Emergency Advance',
        ];

        foreach ($advanceTypes as $name) {
            AdvanceType::query()->firstOrCreate(['name' => $name]);
        }
    }
}
