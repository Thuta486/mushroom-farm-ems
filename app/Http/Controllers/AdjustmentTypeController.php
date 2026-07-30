<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdjustmentTypeRequest;
use App\Models\AdjustmentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdjustmentTypeController extends Controller
{
    public function index(): View
    {
        $adjustmentTypes = AdjustmentType::query()
            ->withCount('payrollAdjustments')
            ->orderBy('name_en')
            ->get();

        return view('adjustment-types.index', compact('adjustmentTypes'));
    }

    public function create(): View
    {
        return view('adjustment-types.create');
    }

    public function store(StoreAdjustmentTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['name']) && ! empty($data['name_en'])) {
            $data['name'] = $data['name_en'];
        }

        AdjustmentType::create($data);

        return redirect()
            ->route('adjustment-types.index')
            ->with('success', 'Adjustment type added successfully.');
    }

    public function edit(AdjustmentType $adjustmentType): View
    {
        return view('adjustment-types.edit', compact('adjustmentType'));
    }

    public function update(StoreAdjustmentTypeRequest $request, AdjustmentType $adjustmentType): RedirectResponse
    {
        $validated = $request->validated();
        if (empty($validated['name']) && ! empty($validated['name_en'])) {
            $validated['name'] = $validated['name_en'];
        }

        $adjustmentType->update($validated);

        return redirect()
            ->route('adjustment-types.index')
            ->with('success', 'Adjustment type updated successfully.');
    }

    public function destroy(AdjustmentType $adjustmentType): RedirectResponse
    {
        if ($adjustmentType->payrollAdjustments()->exists()) {
            return back()->with('error', 'Cannot delete an adjustment type that has payroll adjustments recorded against it.');
        }

        $adjustmentType->delete();

        return redirect()
            ->route('adjustment-types.index')
            ->with('success', 'Adjustment type deleted successfully.');
    }
}
