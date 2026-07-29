<?php

namespace Database\Factories;

use App\Models\AdvanceType;
use App\Models\CashAdvance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashAdvance>
 */
class CashAdvanceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'advance_type_id' => AdvanceType::factory(),
            'date' => fake()->date(),
            'amount' => fake()->numberBetween(10000, 100000),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
