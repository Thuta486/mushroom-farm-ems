<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SalaryHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $employees = Employee::query()
            ->with('department')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('search').'%');
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->integer('department_id'));
            })
            ->when($request->input('employment_status', 'active') !== 'all', function ($query) use ($request) {
                $query->where('employment_status', $request->input('employment_status', 'active'));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('employees.index', [
            'employees' => $employees,
            'departments' => Department::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        return view('employees.create', [
            'departments' => Department::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = DB::transaction(function () use ($request) {
            $validated = $request->safe()->except(['allowed_days_per_month']);

            $employee = Employee::create($validated);

            $employee->holiday()->create([
                'allowed_days_per_month' => $request->integer('allowed_days_per_month'),
            ]);

            $employee->salaryHistories()->create([
                'wage_amount' => $request->input('wage_amount'),
                'effective_date' => $request->input('joining_date'),
                'reason' => 'Initial salary',
            ]);

            return $employee;
        });

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee added successfully.');
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'department',
            'holiday',
            'salaryHistories' => fn ($query) => $query->latest('effective_date')->limit(10),
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $employee->load('holiday');

        return view('employees.edit', [
            'employee' => $employee,
            'departments' => Department::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        DB::transaction(function () use ($request, $employee): void {
            $validated = $request->safe()->except(['allowed_days_per_month', 'salary_change_reason']);

            $previousWage = (string) $employee->wage_amount;
            $newWage = (string) $request->input('wage_amount');

            $employee->update($validated);

            $employee->holiday()->updateOrCreate(
                ['employee_id' => $employee->id],
                ['allowed_days_per_month' => $request->integer('allowed_days_per_month')],
            );

            if ($previousWage !== $newWage) {
                SalaryHistory::create([
                    'employee_id' => $employee->id,
                    'wage_amount' => $request->input('wage_amount'),
                    'effective_date' => now()->toDateString(),
                    'reason' => $request->input('salary_change_reason'),
                ]);
            }
        });

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->payrolls()->exists() || $employee->attendances()->exists()) {
            $employee->update(['employment_status' => 'terminated']);

            return redirect()
                ->route('employees.index')
                ->with('success', 'Employee marked as terminated. Records were kept for payroll and attendance history.');
        }

        DB::transaction(function () use ($employee): void {
            $employee->salaryHistories()->delete();
            $employee->holiday()?->delete();
            $employee->cashAdvances()->delete();
            $employee->delete();
        });

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee removed successfully.');
    }
}
