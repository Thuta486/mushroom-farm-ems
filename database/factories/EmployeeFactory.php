<?php

namespace Database\Factories;

use App\Enums\EmploymentStatus;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
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
            'name' => fake()->name(),
            'phone' => fake()->optional()->phoneNumber(),
            'gender' => fake()->optional()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->optional()->date(),
            'address' => fake()->optional()->address(),
            'joining_date' => fake()->date(),
            'position' => fake()->optional()->randomElement(['Harvester', 'Packer', 'Cleaner', 'Supervisor']),
            'employment_status' => EmploymentStatus::Active,
            'wage_amount' => fake()->numberBetween(150000, 500000),
            'emergency_contact' => fake()->optional()->phoneNumber(),
        ];
    }
}
