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
        AdjustmentType::create($request->validated());

        return redirect()
            ->route('adjustment-types.index')
            ->with('success', __('app.flash.adjustment_type_added'));
    }

    public function edit(AdjustmentType $adjustmentType): View
    {
        return view('adjustment-types.edit', compact('adjustmentType'));
    }

    public function update(StoreAdjustmentTypeRequest $request, AdjustmentType $adjustmentType): RedirectResponse
    {
        $adjustmentType->update($request->validated());

        return redirect()
            ->route('adjustment-types.index')
            ->with('success', __('app.flash.adjustment_type_updated'));
    }

    public function destroy(AdjustmentType $adjustmentType): RedirectResponse
    {
        if ($adjustmentType->payrollAdjustments()->exists()) {
            return back()->with('error', __('app.flash.adjustment_type_delete_blocked'));
        }

        $adjustmentType->delete();

        return redirect()
            ->route('adjustment-types.index')
            ->with('success', __('app.flash.adjustment_type_deleted'));
    }
}