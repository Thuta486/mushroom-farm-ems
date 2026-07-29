<?php

namespace App\Http\Controllers;

use App\Enums\EmploymentStatus;
use App\Http\Requests\StoreCashAdvanceRequest;
use App\Http\Requests\StoreDailyCashAdvanceRequest;
use App\Http\Requests\UpdateCashAdvanceRequest;
use App\Models\AdvanceType;
use App\Models\CashAdvance;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CashAdvanceController extends Controller
{
    public function index(Request $request): View
    {
        $cashAdvances = CashAdvance::query()
            ->with(['employee.department', 'advanceType'])
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('date', '>=', $request->string('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('date', '<=', $request->string('date_to'));
            })
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->integer('employee_id'));
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('cash-advances.index', [
            'cashAdvances' => $cashAdvances,
            'employees' => Employee::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function daily(Request $request): View
    {
        $date = Carbon::parse($request->input('date', now()->toDateString()))->startOfDay();

        $employees = Employee::query()
            ->where('employment_status', EmploymentStatus::Active)
            ->whereDate('joining_date', '<=', $date)
            ->orderBy('name')
            ->get();

        $existingAdvances = CashAdvance::query()
            ->whereDate('date', $date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->orderBy('id')
            ->get();

        return view('cash-advances.daily', [
            'date' => $date,
            'employees' => $employees,
            'existingAdvances' => $existingAdvances,
            'advanceTypes' => AdvanceType::orderBy('name')->pluck('name', 'id'),
            'previousDate' => $date->copy()->subDay()->toDateString(),
            'nextDate' => $date->copy()->addDay()->toDateString(),
        ]);
    }

    public function storeDaily(StoreDailyCashAdvanceRequest $request): RedirectResponse
    {
        $date = $request->date('date')->toDateString();

        $activeEmployeeIds = Employee::query()
            ->where('employment_status', EmploymentStatus::Active)
            ->whereDate('joining_date', '<=', $date)
            ->pluck('id');

        DB::transaction(function () use ($request, $date, $activeEmployeeIds): void {
            // Replace the day's advances for these employees with whatever rows were submitted.
            // This correctly handles edits, new rows, and rows removed in the form.
            CashAdvance::query()
                ->whereDate('date', $date)
                ->whereIn('employee_id', $activeEmployeeIds)
                ->delete();

            foreach ($request->input('advances', []) as $row) {
                $amount = (float) ($row['amount'] ?? 0);

                if ($amount <= 0) {
                    continue;
                }

                CashAdvance::create([
                    'employee_id' => $row['employee_id'],
                    'advance_type_id' => $row['advance_type_id'],
                    'date' => $date,
                    'amount' => $amount,
                    'notes' => $row['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('cash-advances.daily', ['date' => $date])
            ->with('success', 'Cash advances saved for '.$request->date('date')->format('d M Y').'.');
    }

    public function create(): View
    {
        return view('cash-advances.create', [
            'employees' => Employee::orderBy('name')->pluck('name', 'id'),
            'advanceTypes' => AdvanceType::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(StoreCashAdvanceRequest $request): RedirectResponse
    {
        CashAdvance::create($request->validated());

        return redirect()
            ->route('cash-advances.index')
            ->with('success', 'Cash advance recorded.');
    }

    public function edit(CashAdvance $cashAdvance): View
    {
        $cashAdvance->load('employee');

        return view('cash-advances.edit', [
            'cashAdvance' => $cashAdvance,
            'employees' => Employee::orderBy('name')->pluck('name', 'id'),
            'advanceTypes' => AdvanceType::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(UpdateCashAdvanceRequest $request, CashAdvance $cashAdvance): RedirectResponse
    {
        $cashAdvance->update($request->validated());

        return redirect()
            ->route('cash-advances.index')
            ->with('success', 'Cash advance updated.');
    }

    public function destroy(CashAdvance $cashAdvance): RedirectResponse
    {
        $cashAdvance->delete();

        return redirect()
            ->route('cash-advances.index')
            ->with('success', 'Cash advance removed.');
    }
}