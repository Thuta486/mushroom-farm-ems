<?php

namespace Database\Factories;

use App\Enums\PayrollStatus;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grossSalary = fake()->numberBetween(100000, 500000);
        $totalAdvances = fake()->numberBetween(0, 50000);
        $totalBonus = fake()->numberBetween(0, 20000);
        $totalDeduction = fake()->numberBetween(0, 10000);

        return [
            'employee_id' => Employee::factory(),
            'month' => fake()->numberBetween(1, 12),
            'year' => fake()->numberBetween(2024, 2026),
            'gross_salary' => $grossSalary,
            'total_worked_days' => fake()->numberBetween(15, 26),
            'total_worked_hours' => fake()->numberBetween(120, 208),
            'total_worked_minutes' => fake()->numberBetween(0, 59),
            'total_bonus' => $totalBonus,
            'total_deduction' => $totalDeduction,
            'total_advances' => $totalAdvances,
            'net_pay' => $grossSalary + $totalBonus - $totalDeduction - $totalAdvances,
            'status' => PayrollStatus::Unpaid,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['status' => PayrollStatus::Paid]);
    }
}
