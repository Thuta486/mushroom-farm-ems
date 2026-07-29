<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\EmploymentStatus;
use App\Enums\PayrollStatus;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function stats(): array
    {
        $today = now()->startOfDay();
        $month = now()->month;
        $year = now()->year;

        $activeEmployeeIds = Employee::query()
            ->where('employment_status', EmploymentStatus::Active)
            ->whereDate('joining_date', '<=', $today)
            ->pluck('id');

        $todayAttendance = Attendance::query()
            ->whereDate('date', $today)
            ->whereIn('employee_id', $activeEmployeeIds)
            ->selectRaw('COUNT(*) as total_marked')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as present_count', [AttendanceStatus::Present->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as absent_count', [AttendanceStatus::Absent->value])
            ->first();

        $payrollSummary = Payroll::query()
            ->where('month', $month)
            ->where('year', $year)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as paid_count', [PayrollStatus::Paid->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as unpaid_count', [PayrollStatus::Unpaid->value])
            ->selectRaw('SUM(net_pay) as total_net_pay')
            ->first();

        $monthAdvances = CashAdvance::query()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(amount) as total_amount')
            ->first();

        $unpaidPayrolls = Payroll::query()
            ->with('employee.department')
            ->where('status', PayrollStatus::Unpaid)
            ->where(function ($query) use ($month, $year): void {
                $query->where('year', '<', $year)
                    ->orWhere(function ($inner) use ($month, $year): void {
                        $inner->where('year', $year)->where('month', '<=', $month);
                    });
            })
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit(5)
            ->get();

        return [
            'totalEmployees' => Employee::count(),
            'activeEmployees' => Employee::where('employment_status', EmploymentStatus::Active)->count(),
            'departments' => Department::withCount('employees')->orderBy('name')->get(),
            'today' => [
                'date' => $today,
                'active_count' => $activeEmployeeIds->count(),
                'marked_count' => (int) ($todayAttendance->total_marked ?? 0),
                'present_count' => (int) ($todayAttendance->present_count ?? 0),
                'absent_count' => (int) ($todayAttendance->absent_count ?? 0),
            ],
            'payrollSummary' => $payrollSummary,
            'monthAdvances' => $monthAdvances,
            'unpaidPayrolls' => $unpaidPayrolls,
            'month' => $month,
            'year' => $year,
        ];
    }
}
