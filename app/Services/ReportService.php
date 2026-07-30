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
     * Pick the display name based on the current app locale.
     */
    private function localized(?string $nameEn, ?string $nameMy): ?string
    {
        return app()->getLocale() === 'my' ? ($nameMy ?? $nameEn) : ($nameEn ?? $nameMy);
    }

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
            ->selectRaw('employees.name_en as employee_name_en')
            ->selectRaw('employees.name_my as employee_name_my')
            ->selectRaw('departments.name_en as department_name_en')
            ->selectRaw('departments.name_my as department_name_my')
            ->selectRaw('COUNT(*) as days_marked')
            ->selectRaw('SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END) as present_days', [AttendanceStatus::Present->value])
            ->selectRaw('SUM(CASE WHEN attendances.status = ? THEN 1 ELSE 0 END) as absent_days', [AttendanceStatus::Absent->value])
            ->selectRaw('SUM(attendances.hours_worked) as total_hours')
            ->selectRaw('SUM(attendances.minutes_worked) as total_minutes')
            ->groupBy('employees.id', 'employees.name_en', 'employees.name_my', 'departments.name_en', 'departments.name_my')
            ->orderBy('employees.name_en')
            ->get()
            ->map(function ($row) {
                $row->employee_name = $this->localized($row->employee_name_en, $row->employee_name_my);
                $row->department_name = $this->localized($row->department_name_en, $row->department_name_my);

                return $row;
            });

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
            ->selectRaw('departments.name_en as department_name_en')
            ->selectRaw('departments.name_my as department_name_my')
            ->selectRaw('COUNT(*) as employee_count')
            ->selectRaw('SUM(payrolls.gross_salary) as total_gross')
            ->selectRaw('SUM(payrolls.total_advances) as total_advances')
            ->selectRaw('SUM(payrolls.net_pay) as total_net_pay')
            ->selectRaw('SUM(CASE WHEN payrolls.status = ? THEN 1 ELSE 0 END) as unpaid_count', [PayrollStatus::Unpaid->value])
            ->groupBy('departments.name_en', 'departments.name_my')
            ->orderBy('departments.name_en')
            ->get()
            ->map(function ($row) {
                $row->department_name = $this->localized($row->department_name_en, $row->department_name_my)
                    ?? 'No department';

                return $row;
            });

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

        // Eloquent models: display_name is handled via accessors on Employee/Department models.
        $rows = (clone $baseQuery)
            ->with(['employee.department', 'advanceType'])
            ->orderByDesc('date')
            ->orderBy(
                Employee::select('name_en')
                    ->whereColumn('employees.id', 'cash_advances.employee_id')
                    ->limit(1),
            )
            ->get();

        $employeeRows = (clone $baseQuery)
            ->join('employees', 'employees.id', '=', 'cash_advances.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->select('employees.id as employee_id')
            ->selectRaw('employees.name_en as employee_name_en')
            ->selectRaw('employees.name_my as employee_name_my')
            ->selectRaw('departments.name_en as department_name_en')
            ->selectRaw('departments.name_my as department_name_my')
            ->selectRaw('COUNT(*) as advance_count')
            ->selectRaw('SUM(cash_advances.amount) as total_amount')
            ->groupBy('employees.id', 'employees.name_en', 'employees.name_my', 'departments.name_en', 'departments.name_my')
            ->orderBy('employees.name_en')
            ->get()
            ->map(function ($row) {
                $row->employee_name = $this->localized($row->employee_name_en, $row->employee_name_my);
                $row->department_name = $this->localized($row->department_name_en, $row->department_name_my);

                return $row;
            });

        return [
            'summary' => $summary,
            'rows' => $rows,
            'employeeRows' => $employeeRows,
        ];
    }
}