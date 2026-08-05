<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index(): View
    {
        return view('reports.index');
    }

    public function attendance(Request $request): View
    {
        $dateFrom = Carbon::parse($request->input('date_from', now()->startOfMonth()->toDateString()))->startOfDay();
        $dateTo = Carbon::parse($request->input('date_to', now()->toDateString()))->startOfDay();
        $departmentId = $request->filled('department_id') ? $request->integer('department_id') : null;
        $employeeId = $request->filled('employee_id') ? $request->integer('employee_id') : null;

        return view('reports.attendance', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'report' => $this->reports->attendanceReport($dateFrom, $dateTo, $departmentId, $employeeId),
            'employees' => Employee::orderBy('name_en', 'asc')->get()->mapWithKeys(fn ($e) => [$e->id => $e->display_name]),
            'departments' => Department::orderBy('name_en', 'asc')->get()->mapWithKeys(fn ($d) => [$d->id => $d->display_name]),
        ]);
    }

    public function payroll(Request $request): View
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $departmentId = $request->filled('department_id') ? $request->integer('department_id') : null;

        return view('reports.payroll', [
            'month' => $month,
            'year' => $year,
            'report' => $this->reports->payrollReport($month, $year, $departmentId),
            'departments' => Department::orderBy('name_en')->get()->mapWithKeys(fn ($d) => [$d->id => $d->display_name]),
        ]);
    }

    public function cashAdvances(Request $request): View
    {
        $dateFrom = Carbon::parse($request->input('date_from', now()->startOfMonth()->toDateString()))->startOfDay();
        $dateTo = Carbon::parse($request->input('date_to', now()->toDateString()))->startOfDay();
        $employeeId = $request->filled('employee_id') ? $request->integer('employee_id') : null;

        return view('reports.cash-advances', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'report' => $this->reports->cashAdvanceReport($dateFrom, $dateTo, $employeeId),
            'employees' => Employee::orderBy('name_en')->get()->mapWithKeys(fn ($e) => [$e->id => $e->display_name]),
        ]);
    }
}