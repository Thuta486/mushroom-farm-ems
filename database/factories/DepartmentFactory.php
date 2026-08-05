<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => fake()->unique()->randomElement([
                'Administration',
                'Logistics',
                'Mixing & Packaging',
                'Steaming',
                'Spawn Filling',
                'Incubation',
                'Fruiting House',
                'Cleaning',
                'Harvesting',
            ]),
            'name_my' => fake()->unique()->randomElement([
                'စီမံရေးရာ',
                'ပို့ဆောင်ရေးနှင့် ထောက်ပံ့ရေး',
                'ရောစပ်ခြင်းနှင့် ထုပ်ပိုးခြင်း',
                'ပေါင်းခံခြင်း',
                'မှိုမျိုးထည့်သွင်းခြင်း',
                'မှိုမျိုးပွားခန်း',
                'မှိုထွက်ခန်း',
                'သန့်ရှင်းရေး',
                'မှိုခူးယူရေး',
            ]),
        ];
    }
}
