<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Harvesting',
            'Packaging',
            'Cleaning',
            'Spawn Production',
            'Maintenance',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(['name' => $name]);
        }
    }
}
