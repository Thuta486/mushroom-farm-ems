<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdvanceTypeRequest;
use App\Models\AdvanceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdvanceTypeController extends Controller
{
    public function index(): View
    {
        $advanceTypes = AdvanceType::query()
            ->withCount('cashAdvances')
            ->orderBy('name_en')
            ->get();

        return view('advance-types.index', compact('advanceTypes'));
    }

    public function create(): View
    {
        return view('advance-types.create');
    }

    public function store(StoreAdvanceTypeRequest $request): RedirectResponse
    {
        AdvanceType::create($request->validated());

        return redirect()
            ->route('advance-types.index')
            ->with('success', __('app.flash.advance_type_added'));
    }

    public function edit(AdvanceType $advanceType): View
    {
        return view('advance-types.edit', compact('advanceType'));
    }

    public function update(StoreAdvanceTypeRequest $request, AdvanceType $advanceType): RedirectResponse
    {
        $validated = $request->validated();

        $advanceType->update($validated);

        return redirect()
            ->route('advance-types.index')
            ->with('success', __('app.flash.advance_type_updated'));
    }

    public function destroy(AdvanceType $advanceType): RedirectResponse
    {
        if ($advanceType->cashAdvances()->exists()) {
            return back()->with('error', __('app.flash.advance_type_delete_blocked'));
        }

        $advanceType->delete();

        return redirect()
            ->route('advance-types.index')
            ->with('success', __('app.flash.advance_type_deleted'));
    }
}