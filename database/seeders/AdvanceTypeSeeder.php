<?php

namespace Database\Seeders;

use App\Models\AdvanceType;
use Illuminate\Database\Seeder;

class AdvanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $advanceTypes = [
            ['name_en' => 'Salary Advance', 'name_my' => 'လစာကြိုတင်ထုတ်ယူခြင်း'],
            ['name_en' => 'Food Advance', 'name_my' => 'စားစရိတ် ကြိုတင်ထုတ်ယူခြင်း'],
        ];

        foreach ($advanceTypes as $type) {
            AdvanceType::query()->firstOrCreate(
                ['name_en' => $type['name_en']],
                ['name_my' => $type['name_my']]
            );
        }
    }
}
