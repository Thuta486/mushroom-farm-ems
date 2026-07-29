<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Support\Carbon;

class PayrollCalculator
{
    private const MINUTES_PER_WORK_DAY = 8 * 60;

    /**
     * @return array{
     *     gross_salary: string,
     *     total_worked_days: int,
     *     total_worked_hours: int,
     *     total_worked_minutes: int,
     *     total_absent_days: int,
     *     total_absent_hours: int,
     *     total_absent_minutes: int,
     *     attendance_deduction: string,
     *     total_bonus: string,
     *     total_deduction: string,
     *     total_advances: string,
     *     net_pay: string,
     * }
     */
    public function calculate(Employee $employee, int $month, int $year, float $manualBonus = 0, float $manualDeduction = 0): array
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $attendances = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->get();

        $presentAttendances = $attendances->where('status', AttendanceStatus::Present);
        $absentDaysCount = $attendances->where('status', AttendanceStatus::Absent)->count();

        $totalWorkedHours = (int) $presentAttendances->sum('hours_worked');
        $totalWorkedMinutes = (int) $presentAttendances->sum('minutes_worked');

        // Minutes missed on days marked "present" but with less than a full 8-hour day logged.
        $shortfallMinutes = $presentAttendances->sum(function (Attendance $attendance) {
            $workedMinutes = ((int) $attendance->hours_worked * 60) + (int) $attendance->minutes_worked;

            return max(0, self::MINUTES_PER_WORK_DAY - $workedMinutes);
        });

        $totalAbsentMinutes = ($absentDaysCount * self::MINUTES_PER_WORK_DAY) + $shortfallMinutes;

        // Employee's fixed monthly holiday allowance (in days) is excused from deduction.
        $allowedHolidayDays = $employee->holiday?->allowed_days_per_month ?? 2;
        $holidayMinutes = $allowedHolidayDays * self::MINUTES_PER_WORK_DAY;

        $deductibleMinutes = max(0, $totalAbsentMinutes - $holidayMinutes);

        $totalAbsentDays = intdiv($deductibleMinutes, self::MINUTES_PER_WORK_DAY);
        $remainderMinutes = $deductibleMinutes % self::MINUTES_PER_WORK_DAY;
        $totalAbsentHours = intdiv($remainderMinutes, 60);
        $totalAbsentMinutesRemainder = $remainderMinutes % 60;

        // Rate breakdown: daily = salary * 12 / 365, hourly = daily / 8, per-minute = hourly / 60.
        $wageAmount = (float) $employee->wage_amount;
        $dailyRate = $wageAmount * 12 / 365;
        $hourlyRate = $dailyRate / 8;
        $minuteRate = $hourlyRate / 60;

        $attendanceDeduction = round($deductibleMinutes * $minuteRate, 2);

        $grossSalary = round($wageAmount, 2);
        $totalDeduction = round($attendanceDeduction + $manualDeduction, 2);

        $totalAdvances = CashAdvance::query()
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        $netPay = round($grossSalary + $manualBonus - $totalDeduction - (float) $totalAdvances, 2);

        return [
            'gross_salary' => number_format($grossSalary, 2, '.', ''),
            'total_worked_days' => $presentAttendances->count(),
            'total_worked_hours' => $totalWorkedHours,
            'total_worked_minutes' => $totalWorkedMinutes,
            'total_absent_days' => $totalAbsentDays,
            'total_absent_hours' => $totalAbsentHours,
            'total_absent_minutes' => $totalAbsentMinutesRemainder,
            'attendance_deduction' => number_format($attendanceDeduction, 2, '.', ''),
            'total_bonus' => number_format($manualBonus, 2, '.', ''),
            'total_deduction' => number_format($totalDeduction, 2, '.', ''),
            'total_advances' => number_format((float) $totalAdvances, 2, '.', ''),
            'net_pay' => number_format($netPay, 2, '.', ''),
        ];
    }

    /**
     * Returns the manual bonus/deduction totals currently attached to a payroll
     * via payroll_adjustments (excludes the automatic attendance-based deduction).
     *
     * @return array{bonus: float, deduction: float}
     */
    public function manualAdjustmentTotals(Payroll $payroll): array
    {
        return [
            'bonus' => (float) $payroll->payrollAdjustments()
                ->whereHas('adjustmentType', fn ($query) => $query->where('category', 'bonus'))
                ->sum('amount'),
            'deduction' => (float) $payroll->payrollAdjustments()
                ->whereHas('adjustmentType', fn ($query) => $query->where('category', 'deduction'))
                ->sum('amount'),
        ];
    }

    /**
     * Recompute a payroll's totals from its employee's current attendance
     * plus whatever manual bonus/deduction adjustments are attached to it.
     */
    public function recalculateTotals(Payroll $payroll): void
    {
        $manual = $this->manualAdjustmentTotals($payroll);

        $totals = $this->calculate(
            $payroll->employee,
            $payroll->month,
            $payroll->year,
            $manual['bonus'],
            $manual['deduction'],
        );

        $payroll->update([
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
    }
}