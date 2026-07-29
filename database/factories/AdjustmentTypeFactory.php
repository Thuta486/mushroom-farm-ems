<?php

namespace Database\Factories;

use App\Models\AdjustmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdjustmentType>
 */
class AdjustmentTypeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'category' => fake()->randomElement(['bonus', 'deduction']),
        ];
    }

    public function bonus(): static
    {
        return $this->state(fn () => ['category' => 'bonus']);
    }

    public function deduction(): static
    {
        return $this->state(fn () => ['category' => 'deduction']);
    }
}
