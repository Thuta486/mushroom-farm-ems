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
            'name_en' => fake()->unique()->words(2, true),
            'name_my' => null,
        ];
    }

    public function bonus(): static
    {
        return $this->state(fn () => []);
    }

    public function deduction(): static
    {
        return $this->state(fn () => []);
    }
}
