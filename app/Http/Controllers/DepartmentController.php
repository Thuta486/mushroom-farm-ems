<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::query()
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('departments.create');
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department added successfully.');
    }

    public function edit(Department $department): View
    {
        return view('departments.edit', compact('department'));
    }

    public function update(StoreDepartmentRequest $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,'.$department->id],
        ]);

        $department->update($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->employees()->exists()) {
            return back()->with('error', 'Cannot delete a department that has employees assigned.');
        }

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
