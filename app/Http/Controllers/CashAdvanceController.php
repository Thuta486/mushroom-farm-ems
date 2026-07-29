<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCashAdvanceRequest;
use App\Http\Requests\UpdateCashAdvanceRequest;
use App\Models\AdvanceType;
use App\Models\CashAdvance;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
