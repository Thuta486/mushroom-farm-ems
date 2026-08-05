<?php

namespace App\Http\Controllers;

use App\Enums\EmploymentStatus;
use App\Enums\PayrollStatus;
use App\Http\Requests\GeneratePayrollRequest;
use App\Http\Requests\StorePayrollAdjustmentRequest;
use App\Models\AdjustmentType;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollAdjustment;
use App\Services\PayrollCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(private PayrollCalculator $calculator) {}

    public function index(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $payrolls = Payroll::query()
            ->with('employee.department')
            ->where('month', $month)
            ->where('year', $year)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->integer('employee_id'));
            })
            ->orderBy(
                Employee::select('name_en')
                    ->whereColumn('employees.id', 'payrolls.employee_id')
                    ->limit(1),
            )
            ->paginate(20)
            ->withQueryString();

        $summary = Payroll::query()
            ->where('month', $month)
            ->where('year', $year)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as paid_count', [PayrollStatus::Paid->value])
            ->selectRaw('SUM(net_pay) as total_net_pay')
            ->first();

        return view('payrolls.index', [
            'payrolls' => $payrolls,
            'employees' => Employee::orderBy('name_en', 'asc')->get()->pluck('display_name', 'id'),
            'month' => $month,
            'year' => $year,
            'summary' => $summary,
        ]);
    }

    public function generate(): View
    {
        return view('payrolls.generate', [
            'month' => now()->subMonth()->month,
            'year' => now()->subMonth()->year,
        ]);
    }

    public function store(GeneratePayrollRequest $request): RedirectResponse
    {
        $month = $request->integer('month');
        $year = $request->integer('year');
        $periodEnd = Carbon::create($year, $month, 1)->endOfMonth();

        $employees = Employee::query()
            ->with('holiday')
            ->whereIn('employment_status', [EmploymentStatus::Active, EmploymentStatus::Inactive])
            ->whereDate('joining_date', '<=', $periodEnd)
            ->orderBy('name_en')
            ->get();

        $generated = 0;
        $skipped = 0;

        DB::transaction(function () use ($employees, $month, $year, &$generated, &$skipped): void {
            foreach ($employees as $employee) {
                $existing = Payroll::query()
                    ->where('employee_id', $employee->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                if ($existing) {
                    if ($existing->status === PayrollStatus::Paid) {
                        $skipped++;

                        continue;
                    }

                    $manual = $this->calculator->manualAdjustmentTotals($existing);

                    $totals = $this->calculator->calculate(
                        $employee,
                        $month,
                        $year,
                        $manual['bonus'],
                        $manual['deduction'],
                    );

                    $existing->update([
                        'gross_salary' => $totals['gross_salary'],
                        'total_worked_days' => $totals['total_worked_days'],
                        'total_worked_hours' => $totals['total_worked_hours'],
                        'total_worked_minutes' => $totals['total_worked_minutes'],
                        'total_absent_days' => $totals['total_absent_days'],
                        'total_absent_hours' => $totals['total_absent_hours'],
                        'total_absent_minutes' => $totals['total_absent_minutes'],
                        'total_bonus' => $totals['total_bonus'],
                        'total_deduction' => $totals['total_deduction'],
                        'total_advances' => $totals['total_advances'],
                        'net_pay' => $totals['net_pay'],
                    ]);

                    $generated++;

                    continue;
                }

                $totals = $this->calculator->calculate($employee, $month, $year);

                Payroll::create([
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                    'gross_salary' => $totals['gross_salary'],
                    'total_worked_days' => $totals['total_worked_days'],
                    'total_worked_hours' => $totals['total_worked_hours'],
                    'total_worked_minutes' => $totals['total_worked_minutes'],
                    'total_absent_days' => $totals['total_absent_days'],
                    'total_absent_hours' => $totals['total_absent_hours'],
                    'total_absent_minutes' => $totals['total_absent_minutes'],
                    'total_bonus' => $totals['total_bonus'],
                    'total_deduction' => $totals['total_deduction'],
                    'total_advances' => $totals['total_advances'],
                    'net_pay' => $totals['net_pay'],
                    'status' => PayrollStatus::Unpaid,
                ]);

                $generated++;
            }
        });

        $message = __('app.flash.payroll_generated', ['count' => $generated]);
        if ($skipped > 0) {
            $message .= ' '.__('app.flash.payroll_generated_skipped', ['skipped' => $skipped]);
        }

        return redirect()
            ->route('payrolls.index', ['month' => $month, 'year' => $year])
            ->with('success', $message);
    }

    public function show(Payroll $payroll): View
    {
        $payroll->load([
            'employee.department',
            'payrollAdjustments.adjustmentType',
        ]);

        return view('payrolls.show', [
            'payroll' => $payroll,
            'adjustmentTypes' => AdjustmentType::orderBy('name_en')->get(),
        ]);
    }

    public function markPaid(Payroll $payroll): RedirectResponse
    {
        if ($payroll->status === PayrollStatus::Paid) {
            return redirect()
                ->route('payrolls.show', $payroll)
                ->with('error', __('app.flash.payroll_already_paid'));
        }

        $payroll->update(['status' => PayrollStatus::Paid]);

        return redirect()
            ->route('payrolls.show', $payroll)
            ->with('success', __('app.flash.payroll_marked_paid'));
    }

    public function markUnpaid(Payroll $payroll): RedirectResponse
    {
        if ($payroll->status === PayrollStatus::Unpaid) {
            return redirect()
                ->route('payrolls.show', $payroll)
                ->with('error', __('app.flash.payroll_already_unpaid'));
        }

        $payroll->update(['status' => PayrollStatus::Unpaid]);

        return redirect()
            ->route('payrolls.show', $payroll)
            ->with('success', __('app.flash.payroll_marked_unpaid'));
    }

    public function storeAdjustment(StorePayrollAdjustmentRequest $request, Payroll $payroll): RedirectResponse
    {
        if ($payroll->status === PayrollStatus::Paid) {
            return redirect()
                ->route('payrolls.show', $payroll)
                ->with('error', __('app.flash.payroll_cannot_add_adjustments_paid'));
        }

        DB::transaction(function () use ($request, $payroll): void {
            PayrollAdjustment::create([
                'payroll_id' => $payroll->id,
                ...$request->validated(),
            ]);

            $this->calculator->recalculateTotals($payroll->fresh(['payrollAdjustments.adjustmentType']));
        });

        return redirect()
            ->route('payrolls.show', $payroll)
            ->with('success', __('app.flash.adjustment_added'));
    }

    public function destroyAdjustment(Payroll $payroll, PayrollAdjustment $adjustment): RedirectResponse
    {
        if ($payroll->status === PayrollStatus::Paid) {
            return redirect()
                ->route('payrolls.show', $payroll)
                ->with('error', __('app.flash.payroll_cannot_remove_adjustments_paid'));
        }

        if ($adjustment->payroll_id !== $payroll->id) {
            abort(404);
        }

        DB::transaction(function () use ($payroll, $adjustment): void {
            $adjustment->delete();
            $this->calculator->recalculateTotals($payroll->fresh(['payrollAdjustments.adjustmentType']));
        });

        return redirect()
            ->route('payrolls.show', $payroll)
            ->with('success', __('app.flash.adjustment_removed'));
    }
}
