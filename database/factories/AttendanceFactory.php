<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(AttendanceStatus::cases());
        $isPresent = $status === AttendanceStatus::Present;

        return [
            'employee_id' => Employee::factory(),
            'date' => fake()->date(),
            'status' => $status,
            'hours_worked' => $isPresent ? fake()->numberBetween(6, 10) : 0,
            'minutes_worked' => $isPresent ? fake()->randomElement([0, 15, 30, 45]) : 0,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function present(): static
    {
        return $this->state(fn () => [
            'status' => AttendanceStatus::Present,
            'hours_worked' => 8,
            'minutes_worked' => 0,
        ]);
    }

    public function absent(): static
    {
        return $this->state(fn () => [
            'status' => AttendanceStatus::Absent,
            'hours_worked' => 0,
            'minutes_worked' => 0,
        ]);
    }
}
