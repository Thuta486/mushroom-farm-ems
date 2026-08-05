<?php

namespace Database\Factories;

use App\Enums\EmploymentStatus;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'name_en' => fake()->name(),
            'name_my' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->date(),
            'address' => fake()->address(),
            'joining_date' => fake()->date(),
            'position_en' => fake()->randomElement(['Harvester', 'Packer', 'Cleaner', 'Supervisor', 'Accountant']),
            'position_my' => fake()->randomElement(['Harvester', 'Packer', 'Cleaner', 'Supervisor', 'Accountant']),
            'employment_status' => EmploymentStatus::Active,
            'wage_amount' => fake()->numberBetween(150000, 500000),
            'age' => fake()->numberBetween(18, 65),
            'emergency_contact' => fake()->phoneNumber(),
        ];
    }
}
