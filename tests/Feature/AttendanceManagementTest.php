<?php

use App\Enums\AttendanceStatus;
use App\Enums\EmploymentStatus;
use App\Enums\WorkType;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('attendance history page can be rendered', function () {
    $attendance = Attendance::factory()->present()->create();

    $this->get(route('attendances.index'))
        ->assertSuccessful()
        ->assertSee($attendance->employee->name);
});

test('daily attendance page lists active employees', function () {
    $employee = Employee::factory()->create([
        'name' => 'Aung Aung',
        'employment_status' => EmploymentStatus::Active,
        'joining_date' => now()->subMonth(),
    ]);

    $this->get(route('attendances.daily'))
        ->assertSuccessful()
        ->assertSee('Aung Aung')
        ->assertSee('Daily Attendance');
});

test('inactive employees are excluded from daily attendance sheet', function () {
    Employee::factory()->create([
        'name' => 'Inactive Worker',
        'employment_status' => EmploymentStatus::Inactive,
    ]);

    $this->get(route('attendances.daily'))
        ->assertSuccessful()
        ->assertDontSee('Inactive Worker');
});

test('daily attendance can be saved for multiple employees', function () {
    $firstEmployee = Employee::factory()->create(['employment_status' => EmploymentStatus::Active]);
    $secondEmployee = Employee::factory()->create(['employment_status' => EmploymentStatus::Active]);
    $date = '2026-07-15';

    $this->post(route('attendances.daily.store'), [
        'date' => $date,
        'attendances' => [
            [
                'employee_id' => $firstEmployee->id,
                'status' => AttendanceStatus::Present->value,
                'hours_worked' => 8,
                'minutes_worked' => 0,
                'work_type' => WorkType::Harvesting->value,
                'notes' => 'Morning shift',
            ],
            [
                'employee_id' => $secondEmployee->id,
                'status' => AttendanceStatus::Absent->value,
                'hours_worked' => 0,
                'minutes_worked' => 0,
            ],
        ],
    ])->assertRedirect(route('attendances.daily', ['date' => $date]));

    expect(Attendance::whereDate('date', $date)->count())->toBe(2)
        ->and(Attendance::where('employee_id', $firstEmployee->id)->first())
        ->status->toBe(AttendanceStatus::Present)
        ->and(Attendance::where('employee_id', $secondEmployee->id)->first())
        ->hours_worked->toBe(0);
});

test('saving daily attendance updates existing records for the same date', function () {
    $employee = Employee::factory()->create(['employment_status' => EmploymentStatus::Active]);
    $date = '2026-07-10';

    Attendance::factory()->for($employee)->create([
        'date' => $date,
        'status' => AttendanceStatus::Absent,
        'hours_worked' => 0,
        'minutes_worked' => 0,
    ]);

    $this->post(route('attendances.daily.store'), [
        'date' => $date,
        'attendances' => [
            [
                'employee_id' => $employee->id,
                'status' => AttendanceStatus::Present->value,
                'hours_worked' => 7,
                'minutes_worked' => 30,
                'work_type' => WorkType::Cleaning->value,
            ],
        ],
    ])->assertRedirect();

    expect(Attendance::where('employee_id', $employee->id)->whereDate('date', $date)->count())->toBe(1)
        ->and($employee->attendances()->first()->hours_worked)->toBe(7);
});

test('present attendance requires hours or minutes', function () {
    $employee = Employee::factory()->create(['employment_status' => EmploymentStatus::Active]);

    $this->from(route('attendances.daily'))
        ->post(route('attendances.daily.store'), [
            'date' => now()->toDateString(),
            'attendances' => [
                [
                    'employee_id' => $employee->id,
                    'status' => AttendanceStatus::Present->value,
                    'hours_worked' => 0,
                    'minutes_worked' => 0,
                ],
            ],
        ])
        ->assertRedirect(route('attendances.daily'))
        ->assertSessionHasErrors('attendances.0.hours_worked');
});

test('an attendance record can be updated', function () {
    $attendance = Attendance::factory()->present()->create([
        'hours_worked' => 8,
        'minutes_worked' => 0,
    ]);

    $this->put(route('attendances.update', $attendance), [
        'status' => AttendanceStatus::Present->value,
        'hours_worked' => 6,
        'minutes_worked' => 15,
        'work_type' => WorkType::Packaging->value,
        'notes' => 'Half day packaging',
    ])->assertRedirect();

    expect($attendance->fresh())
        ->hours_worked->toBe(6)
        ->minutes_worked->toBe(15)
        ->work_type->toBe(WorkType::Packaging);
});

test('an attendance record can be removed', function () {
    $attendance = Attendance::factory()->create();

    $this->delete(route('attendances.destroy', $attendance))
        ->assertRedirect();

    expect(Attendance::find($attendance->id))->toBeNull();
});

test('employee show page displays recent attendance', function () {
    $employee = Employee::factory()->create(['name' => 'Ko Zaw']);
    $employee->holiday()->create(['allowed_days_per_month' => 2]);

    Attendance::factory()->for($employee)->present()->create([
        'date' => '2026-07-01',
    ]);

    $this->get(route('employees.show', $employee))
        ->assertSuccessful()
        ->assertSee('Recent Attendance')
        ->assertSee('01 Jul 2026');
});
