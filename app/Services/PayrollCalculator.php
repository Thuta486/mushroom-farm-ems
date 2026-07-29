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
    /**
     * @return array{
     *     gross_salary: string,
     *     total_worked_days: int,
     *     total_worked_hours: int,
     *     total_worked_minutes: int,
     *     total_advances: string,
     *     net_pay: string,
     * }
     */
    public function calculate(Employee $employee, int $month, int $year, float $totalBonus = 0, float $totalDeduction = 0): array
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        $daysInMonth = $periodStart->daysInMonth;

        $attendances = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->get();

        $presentDays = $attendances->where('status', AttendanceStatus::Present)->count();
        $absentDays = $attendances->where('status', AttendanceStatus::Absent)->count();

        $allowedHolidayDays = $employee->holiday?->allowed_days_per_month ?? 2;
        $excessAbsences = max(0, $absentDays - $allowedHolidayDays);
        $payableDays = min($daysInMonth, $presentDays + min($absentDays, $allowedHolidayDays));

        $grossSalary = round(($payableDays / $daysInMonth) * (float) $employee->wage_amount, 2);

        $totalWorkedHours = $attendances
            ->where('status', AttendanceStatus::Present)
            ->sum('hours_worked');

        $totalWorkedMinutes = $attendances
            ->where('status', AttendanceStatus::Present)
            ->sum('minutes_worked');

        $totalAdvances = CashAdvance::query()
            ->where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        $netPay = round($grossSalary + $totalBonus - $totalDeduction - (float) $totalAdvances, 2);

        return [
            'gross_salary' => number_format($grossSalary, 2, '.', ''),
            'total_worked_days' => $presentDays,
            'total_worked_hours' => (int) $totalWorkedHours,
            'total_worked_minutes' => (int) $totalWorkedMinutes,
            'total_advances' => number_format((float) $totalAdvances, 2, '.', ''),
            'net_pay' => number_format($netPay, 2, '.', ''),
            'excess_absences' => $excessAbsences,
        ];
    }

    public function recalculateTotals(Payroll $payroll): void
    {
        $totalBonus = (float) $payroll->payrollAdjustments()
            ->whereHas('adjustmentType', fn ($query) => $query->where('category', 'bonus'))
            ->sum('amount');

        $totalDeduction = (float) $payroll->payrollAdjustments()
            ->whereHas('adjustmentType', fn ($query) => $query->where('category', 'deduction'))
            ->sum('amount');

        $payroll->update([
            'total_bonus' => number_format($totalBonus, 2, '.', ''),
            'total_deduction' => number_format($totalDeduction, 2, '.', ''),
            'net_pay' => number_format(
                (float) $payroll->gross_salary + $totalBonus - $totalDeduction - (float) $payroll->total_advances,
                2,
                '.',
                '',
            ),
        ]);
    }
}
