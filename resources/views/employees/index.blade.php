@extends('layouts.app')

@section('title', 'Employees')

@section('content')
    @php
        use App\Enums\EmploymentStatus;
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Employees</h1>
            <p class="mt-1 text-sm text-stone-500">Manage farm workers and their details</p>
        </div>
        <x-button href="{{ route('employees.create') }}">Add Employee</x-button>
    </div>

    <form method="GET" action="{{ route('employees.index') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-4">
        <x-form-input name="search" label="Search by name" :value="request('search')" />
        <x-form-select name="department_id" label="Department" :options="$departments" :selected="request('department_id')" placeholder="All departments" />
        <x-form-select name="employment_status" label="Status" :options="['all' => 'All statuses'] + EmploymentStatus::options()" :selected="request('employment_status', 'active')" />
        <div class="flex items-end gap-2">
            <x-button type="submit" class="w-full">Filter</x-button>
            <x-button href="{{ route('employees.index') }}" variant="secondary" class="w-full">Reset</x-button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Wage</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employees as $employee)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('employees.show', $employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">
                                {{ $employee->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $employee->department?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $employee->position ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ number_format($employee->wage_amount, 0) }} MMK</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$employee->employment_status->value" />
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('employees.edit', $employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-stone-500">
                            No employees found. Try changing the filters or add a new employee.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($employees->hasPages())
        <div class="mt-6">
            {{ $employees->links() }}
        </div>
    @endif
@endsection
