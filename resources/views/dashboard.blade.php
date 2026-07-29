@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-stone-900">Dashboard</h1>
        <p class="mt-1 text-sm text-stone-500">Overview of your farm workforce</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">Total Employees</p>
            <p class="mt-2 text-3xl font-semibold text-stone-900">{{ $totalEmployees }}</p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">Active Employees</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-700">{{ $activeEmployees }}</p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">Departments</p>
            <p class="mt-2 text-3xl font-semibold text-stone-900">{{ $departments->count() }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-xl border border-stone-200 bg-white">
        <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-stone-900">Employees by Department</h2>
            <x-button href="{{ route('employees.create') }}">Add Employee</x-button>
        </div>

        <div class="divide-y divide-stone-100">
            @forelse ($departments as $department)
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <p class="font-medium text-stone-900">{{ $department->name }}</p>
                        <p class="text-sm text-stone-500">{{ $department->employees_count }} employees</p>
                    </div>
                    <a href="{{ route('employees.index', ['department_id' => $department->id]) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                        View employees
                    </a>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-sm text-stone-500">
                    No departments yet.
                    <a href="{{ route('departments.create') }}" class="font-medium text-emerald-700">Add a department</a>
                    to get started.
                </div>
            @endforelse
        </div>
    </div>
@endsection
