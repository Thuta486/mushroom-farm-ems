<?php

use App\Enums\EmploymentStatus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SalaryHistory;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('employees index page can be rendered', function () {
    Employee::factory()->create(['name' => 'Aung Aung']);

    $this->get(route('employees.index'))
        ->assertSuccessful()
        ->assertSee('Aung Aung');
});

test('an employee can be created with holiday and salary history', function () {
    $department = Department::factory()->create();

    $this->post(route('employees.store'), [
        'department_id' => $department->id,
        'name' => 'Ma Thida',
        'phone' => '09123456789',
        'gender' => 'female',
        'date_of_birth' => '1995-05-10',
        'address' => 'Yangon',
        'joining_date' => '2024-01-15',
        'position' => 'Harvester',
        'employment_status' => EmploymentStatus::Active->value,
        'wage_amount' => 250000,
        'emergency_contact' => '09987654321',
        'allowed_days_per_month' => 2,
    ])->assertRedirect();

    $employee = Employee::where('name', 'Ma Thida')->first();

    expect($employee)->not->toBeNull()
        ->and($employee->holiday)->not->toBeNull()
        ->and($employee->holiday->allowed_days_per_month)->toBe(2)
        ->and($employee->salaryHistories)->toHaveCount(1)
        ->and($employee->salaryHistories->first()->reason)->toBe('Initial salary');
});

test('updating wage creates a salary history record', function () {
    $employee = Employee::factory()->create(['wage_amount' => 200000]);
    $employee->holiday()->create(['allowed_days_per_month' => 2]);

    $this->put(route('employees.update', $employee), [
        'department_id' => $employee->department_id,
        'name' => $employee->name,
        'joining_date' => $employee->joining_date->format('Y-m-d'),
        'employment_status' => EmploymentStatus::Active->value,
        'wage_amount' => 250000,
        'allowed_days_per_month' => 2,
        'salary_change_reason' => 'Annual increment',
    ])->assertRedirect(route('employees.show', $employee));

    expect(SalaryHistory::where('employee_id', $employee->id)->count())->toBe(1)
        ->and($employee->fresh()->wage_amount)->toBe('250000.00');
});

test('wage change requires a reason', function () {
    $employee = Employee::factory()->create(['wage_amount' => 200000]);
    $employee->holiday()->create(['allowed_days_per_month' => 2]);

    $this->from(route('employees.edit', $employee))
        ->put(route('employees.update', $employee), [
            'name' => $employee->name,
            'joining_date' => $employee->joining_date->format('Y-m-d'),
            'employment_status' => EmploymentStatus::Active->value,
            'wage_amount' => 300000,
            'allowed_days_per_month' => 2,
        ])
        ->assertRedirect(route('employees.edit', $employee))
        ->assertSessionHasErrors('salary_change_reason');
});

test('employee show page displays profile details', function () {
    $employee = Employee::factory()->create(['name' => 'Ko Zaw']);
    $employee->holiday()->create(['allowed_days_per_month' => 2]);
    $employee->salaryHistories()->create([
        'wage_amount' => $employee->wage_amount,
        'effective_date' => $employee->joining_date,
        'reason' => 'Initial salary',
    ]);

    $this->get(route('employees.show', $employee))
        ->assertSuccessful()
        ->assertSee('Ko Zaw')
        ->assertSee('Initial salary');
});

test('departments can be managed', function () {
    $this->post(route('departments.store'), ['name' => 'Quality Control'])
        ->assertRedirect(route('departments.index'));

    expect(Department::where('name', 'Quality Control')->exists())->toBeTrue();
});
