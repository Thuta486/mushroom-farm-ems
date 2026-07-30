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
            'name_my' => null,
            'phone' => fake()->optional()->phoneNumber(),
            'gender' => fake()->optional()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->optional()->date(),
            'address' => fake()->optional()->address(),
            'joining_date' => fake()->date(),
            'position_en' => fake()->optional()->randomElement(['Harvester', 'Packer', 'Cleaner', 'Supervisor']),
            'position_my' => null,
            'employment_status' => EmploymentStatus::Active,
            'wage_amount' => fake()->numberBetween(150000, 500000),
            'age' => fake()->optional()->numberBetween(18, 65),
            'emergency_contact' => fake()->optional()->phoneNumber(),
        ];
    }
}
