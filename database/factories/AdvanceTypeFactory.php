<?php

namespace Database\Factories;

use App\Models\AdvanceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdvanceType>
 */
class AdvanceTypeFactory extends Factory
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
}
