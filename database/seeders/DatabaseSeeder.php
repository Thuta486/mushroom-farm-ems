<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Farm Accountant',
        //     'email' => 'accountant@mushroomfarm.test',
        //     'role' => 'admin',
        //     'password' => 'password',
        // ]);

        // User::factory()->create([
        //     'name' => 'Farm Manager',
        //     'email' => 'manager@mushroomfarm.test',
        //     'role' => 'superadmin',
        //     'password' => 'password',
        // ]);

        $this->call([
            DepartmentSeeder::class,
            AdjustmentTypeSeeder::class,
            AdvanceTypeSeeder::class,
        ]);
    }
}
