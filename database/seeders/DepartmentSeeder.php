<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name_en' => 'Administration',      'name_my' => 'စီမံရေးရာ'],
            ['name_en' => 'Logistics',           'name_my' => 'ပို့ဆောင်ရေးနှင့် ထောက်ပံ့ရေး'],
            ['name_en' => 'Mixing & Packaging',  'name_my' => 'ရောစပ်ခြင်းနှင့် ထုပ်ပိုးခြင်း'],
            ['name_en' => 'Steaming',            'name_my' => 'ပေါင်းခံခြင်း'],
            ['name_en' => 'Spawn Filling',       'name_my' => 'မှိုမျိုးထည့်သွင်းခြင်း'],
            ['name_en' => 'Incubation',          'name_my' => 'မှိုမျိုးပွားခန်း'],
            ['name_en' => 'Fruiting House',      'name_my' => 'မှိုထွက်ခန်း'],
            ['name_en' => 'Cleaning',            'name_my' => 'သန့်ရှင်းရေး'],
            ['name_en' => 'Harvesting',          'name_my' => 'မှိုခူးယူရေး'],
        ];

        foreach ($departments as $dept) {
            Department::query()->firstOrCreate(
                ['name_en' => $dept['name_en']],
                ['name_my' => $dept['name_my']]
            );
        }
    }
}
