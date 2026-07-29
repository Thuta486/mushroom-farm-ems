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
            ->orderBy('name')
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
            ->with('success', 'Advance type added successfully.');
    }

    public function edit(AdvanceType $advanceType): View
    {
        return view('advance-types.edit', compact('advanceType'));
    }

    public function update(StoreAdvanceTypeRequest $request, AdvanceType $advanceType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:advance_types,name,'.$advanceType->id],
        ]);

        $advanceType->update($validated);

        return redirect()
            ->route('advance-types.index')
            ->with('success', 'Advance type updated successfully.');
    }

    public function destroy(AdvanceType $advanceType): RedirectResponse
    {
        if ($advanceType->cashAdvances()->exists()) {
            return back()->with('error', 'Cannot delete an advance type that has cash advances recorded against it.');
        }

        $advanceType->delete();

        return redirect()
            ->route('advance-types.index')
            ->with('success', 'Advance type deleted successfully.');
    }
}