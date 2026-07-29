<?php

use App\Enums\EmploymentStatus;
use App\Enums\PayrollStatus;
use App\Models\AdjustmentType;
use App\Models\AdvanceType;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Payroll;
use App\Models\PayrollAdjustment;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('payroll index page can be rendered', function () {
    $payroll = Payroll::factory()->create();

    $this->get(route('payrolls.index'))
        ->assertSuccessful()
        ->assertSee($payroll->employee->name);
});

test('cash advances index page can be rendered', function () {
    $cashAdvance = CashAdvance::factory()->create();

    $this->get(route('cash-advances.index'))
        ->assertSuccessful()
        ->assertSee($cashAdvance->employee->name);
});

test('cash advance can be recorded', function () {
    $employee = Employee::factory()->create();
    $advanceType = AdvanceType::factory()->create(['name' => 'Cash Advance']);

    $this->post(route('cash-advances.store'), [
        'employee_id' => $employee->id,
        'advance_type_id' => $advanceType->id,
        'date' => '2026-07-10',
        'amount' => 50000,
        'notes' => 'Emergency need',
    ])->assertRedirect(route('cash-advances.index'));

    expect(CashAdvance::where('employee_id', $employee->id)->count())->toBe(1);
});

test('payroll can be generated from attendance and advances', function () {
    $employee = Employee::factory()->create([
        'employment_status' => EmploymentStatus::Active,
        'joining_date' => '2026-01-01',
        'wage_amount' => 300000,
    ]);

    Holiday::create([
        'employee_id' => $employee->id,
        'allowed_days_per_month' => 2,
    ]);

    Attendance::factory()->present()->create([
        'employee_id' => $employee->id,
        'date' => '2026-07-01',
        'hours_worked' => 8,
        'minutes_worked' => 0,
    ]);

    Attendance::factory()->present()->create([
        'employee_id' => $employee->id,
        'date' => '2026-07-02',
        'hours_worked' => 8,
        'minutes_worked' => 0,
    ]);

    $advanceType = AdvanceType::factory()->create();
    CashAdvance::factory()->create([
        'employee_id' => $employee->id,
        'advance_type_id' => $advanceType->id,
        'date' => '2026-07-05',
        'amount' => 20000,
    ]);

    $this->post(route('payrolls.store'), [
        'month' => 7,
        'year' => 2026,
    ])->assertRedirect(route('payrolls.index', ['month' => 7, 'year' => 2026]));

    $payroll = Payroll::where('employee_id', $employee->id)->first();

    expect($payroll)->not->toBeNull()
        ->and($payroll->total_worked_days)->toBe(2)
        ->and($payroll->total_advances)->toBe('20000.00')
        ->and($payroll->status)->toBe(PayrollStatus::Unpaid)
        ->and((float) $payroll->net_pay)->toBe((float) $payroll->gross_salary - 20000);
});

test('payroll can be marked as paid', function () {
    $payroll = Payroll::factory()->create(['status' => PayrollStatus::Unpaid]);

    $this->post(route('payrolls.mark-paid', $payroll))
        ->assertRedirect(route('payrolls.show', $payroll));

    expect($payroll->fresh()->status)->toBe(PayrollStatus::Paid);
});

test('adjustment can be added to unpaid payroll', function () {
    $payroll = Payroll::factory()->create([
        'gross_salary' => 300000,
        'total_bonus' => 0,
        'total_deduction' => 0,
        'total_advances' => 0,
        'net_pay' => 300000,
        'status' => PayrollStatus::Unpaid,
    ]);

    $bonusType = AdjustmentType::factory()->bonus()->create(['name' => 'Performance Bonus']);

    $this->post(route('payrolls.adjustments.store', $payroll), [
        'adjustment_type_id' => $bonusType->id,
        'amount' => 10000,
        'reason' => 'Good harvest',
    ])->assertRedirect(route('payrolls.show', $payroll));

    $payroll->refresh();

    expect($payroll->total_bonus)->toBe('10000.00')
        ->and($payroll->net_pay)->toBe('310000.00')
        ->and(PayrollAdjustment::where('payroll_id', $payroll->id)->count())->toBe(1);
});

test('adjustments cannot be added to paid payroll', function () {
    $payroll = Payroll::factory()->paid()->create();
    $adjustmentType = AdjustmentType::factory()->create();

    $this->post(route('payrolls.adjustments.store', $payroll), [
        'adjustment_type_id' => $adjustmentType->id,
        'amount' => 5000,
    ])->assertRedirect(route('payrolls.show', $payroll))
        ->assertSessionHas('error');
});

test('regenerating payroll skips paid records', function () {
    $employee = Employee::factory()->create([
        'employment_status' => EmploymentStatus::Active,
        'joining_date' => '2026-01-01',
        'wage_amount' => 200000,
    ]);

    Holiday::create([
        'employee_id' => $employee->id,
        'allowed_days_per_month' => 2,
    ]);

    $paidPayroll = Payroll::factory()->paid()->create([
        'employee_id' => $employee->id,
        'month' => 6,
        'year' => 2026,
        'net_pay' => 150000,
    ]);

    $this->post(route('payrolls.store'), [
        'month' => 6,
        'year' => 2026,
    ])->assertRedirect(route('payrolls.index', ['month' => 6, 'year' => 2026]));

    expect($paidPayroll->fresh()->net_pay)->toBe('150000.00');
});

test('cash advance validation requires amount', function () {
    $employee = Employee::factory()->create();
    $advanceType = AdvanceType::factory()->create();

    $this->from(route('cash-advances.create'))
        ->post(route('cash-advances.store'), [
            'employee_id' => $employee->id,
            'advance_type_id' => $advanceType->id,
            'date' => '2026-07-01',
            'amount' => '',
        ])->assertRedirect(route('cash-advances.create'))
        ->assertSessionHasErrors(['amount']);
});

test('sidebar shows payment navigation links', function () {
    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Payrolls')
        ->assertSee('Cash Advances');
});
