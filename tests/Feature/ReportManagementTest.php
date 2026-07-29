<?php

use App\Enums\PayrollStatus;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('dashboard shows workforce summary', function () {
    $employee = Employee::factory()->create(['name' => 'Farm Worker One']);

    Attendance::factory()->present()->create([
        'employee_id' => $employee->id,
        'date' => now()->toDateString(),
    ]);

    Payroll::factory()->create([
        'employee_id' => $employee->id,
        'month' => now()->month,
        'year' => now()->year,
        'status' => PayrollStatus::Unpaid,
    ]);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Dashboard')
        ->assertSee('Farm Worker One')
        ->assertSee('Unpaid Payrolls');
});

test('reports index page can be rendered', function () {
    $this->get(route('reports.index'))
        ->assertSuccessful()
        ->assertSee('Attendance Report')
        ->assertSee('Payroll Summary')
        ->assertSee('Cash Advance Report');
});

test('attendance report shows employee summary', function () {
    $attendance = Attendance::factory()->present()->create([
        'date' => now()->startOfMonth()->toDateString(),
    ]);

    $this->get(route('reports.attendance', [
        'date_from' => now()->startOfMonth()->toDateString(),
        'date_to' => now()->toDateString(),
    ]))
        ->assertSuccessful()
        ->assertSee('Attendance Report')
        ->assertSee($attendance->employee->name)
        ->assertSee('Present Days');
});

test('payroll report shows department totals', function () {
    $payroll = Payroll::factory()->create([
        'month' => now()->month,
        'year' => now()->year,
    ]);

    $this->get(route('reports.payroll', [
        'month' => now()->month,
        'year' => now()->year,
    ]))
        ->assertSuccessful()
        ->assertSee('Payroll Summary')
        ->assertSee($payroll->employee->department->name);
});

test('cash advance report shows advance records', function () {
    $cashAdvance = CashAdvance::factory()->create([
        'date' => now()->startOfMonth()->addDays(2)->toDateString(),
    ]);

    $this->get(route('reports.cash-advances', [
        'date_from' => now()->startOfMonth()->toDateString(),
        'date_to' => now()->toDateString(),
    ]))
        ->assertSuccessful()
        ->assertSee('Cash Advance Report')
        ->assertSee($cashAdvance->employee->name);
});

test('guests cannot access reports', function () {
    auth()->logout();

    $this->get(route('reports.index'))->assertRedirect(route('login'));
});
