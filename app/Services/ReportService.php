<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\PayrollStatus;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ReportService
{
    /**
     * @return array<string, mixed>
     */
    public function attendanceReport(Carbon $dateFrom, Carbon $dateTo, ?int $departmentId = null, ?int $employeeId = null): array
    {
        $baseQuery = Attendance::query()
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->when($departmentId, function (Builder $query) use ($departmentId): void {
                $query->whereHas('employee', fn (Builder $employeeQuery) => $employeeQuery->where('department_id', $departmentId));
            })
            ->when($employeeId, fn (Builder $query) => $query->where('employee_id', $employeeId));

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_records')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as present_count', [AttendanceStatus::Present->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as absent_count', [AttendanceStatus::Absent->value])
            ->selectRaw('SUM(hours_worked) as total_hours')
            ->selectRaw('SUM(minutes_worked) as total_minutes')
            ->first();

        $rows = (clone $baseQuery)
            ->join('employees', 'employees.id', '=', 'attendances.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->select('employees.id as employee_id')
            ->selectRaw('employees.name as employee_name')
            ->selectRaw('departments.name as department_name')
            ->selectRaw('COUNT(*) as days_marked')
            ->selectRaw('SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END) as present_days', [AttendanceStatus::Present->value])
            ->selectRaw('SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END) as absent_days', [AttendanceStatus::Absent->value])
            ->selectRaw('SUM(attendances.hours_worked) as total_hours')
            ->selectRaw('SUM(attendances.minutes_worked) as total_minutes')
            ->groupBy('employees.id', 'employees.name', 'departments.name')
            ->orderBy('employees.name')
            ->get();

        return [
            'summary' => $summary,
            'rows' => $rows,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payrollReport(int $month, int $year, ?int $departmentId = null): array
    {
        $baseQuery = Payroll::query()
            ->join('employees', 'employees.id', '=', 'payrolls.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->where('payrolls.month', $month)
            ->where('payrolls.year', $year)
            ->when($departmentId, fn (Builder $query) => $query->where('employees.department_id', $departmentId));

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN payrolls.status = ? THEN 1 ELSE 0 END) as paid_count', [PayrollStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN payrolls.status = ? THEN 1 ELSE 0 END) as unpaid_count', [PayrollStatus::Unpaid->value])
            ->selectRaw('SUM(payrolls.gross_salary) as total_gross')
            ->selectRaw('SUM(payrolls.total_advances) as total_advances')
            ->selectRaw('SUM(payrolls.net_pay) as total_net_pay')
            ->first();

        $departmentRows = (clone $baseQuery)
            ->selectRaw('COALESCE(departments.name, ?) as department_name', ['No department'])
            ->selectRaw('COUNT(*) as employee_count')
            ->selectRaw('SUM(payrolls.gross_salary) as total_gross')
            ->selectRaw('SUM(payrolls.total_advances) as total_advances')
            ->selectRaw('SUM(payrolls.net_pay) as total_net_pay')
            ->selectRaw('SUM(CASE WHEN payrolls.status = ? THEN 1 ELSE 0 END) as unpaid_count', [PayrollStatus::Unpaid->value])
            ->groupBy('departments.name')
            ->orderBy('department_name')
            ->get();

        return [
            'summary' => $summary,
            'departmentRows' => $departmentRows,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function cashAdvanceReport(Carbon $dateFrom, Carbon $dateTo, ?int $employeeId = null): array
    {
        $baseQuery = CashAdvance::query()
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo)
            ->when($employeeId, fn (Builder $query) => $query->where('employee_id', $employeeId));

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(amount) as total_amount')
            ->first();

        $rows = (clone $baseQuery)
            ->with(['employee.department', 'advanceType'])
            ->orderByDesc('date')
            ->orderBy(
                Employee::select('name')
                    ->whereColumn('employees.id', 'cash_advances.employee_id')
                    ->limit(1),
            )
            ->get();

        $employeeRows = (clone $baseQuery)
            ->join('employees', 'employees.id', '=', 'cash_advances.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->select('employees.id as employee_id')
            ->selectRaw('employees.name as employee_name')
            ->selectRaw('departments.name as department_name')
            ->selectRaw('COUNT(*) as advance_count')
            ->selectRaw('SUM(cash_advances.amount) as total_amount')
            ->groupBy('employees.id', 'employees.name', 'departments.name')
            ->orderBy('employees.name')
            ->get();

        return [
            'summary' => $summary,
            'rows' => $rows,
            'employeeRows' => $employeeRows,
        ];
    }
}
