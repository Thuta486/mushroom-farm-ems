@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
        $unmarkedToday = max(0, $today['active_count'] - $today['marked_count']);
    @endphp

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Dashboard</h1>
            <p class="mt-1 text-sm text-stone-500">Farm workforce overview for {{ now()->format('d M Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('attendances.daily') }}">Mark Attendance</x-button>
            <x-button href="{{ route('reports.index') }}" variant="secondary">View Reports</x-button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">Active Employees</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-700">{{ $activeEmployees }}</p>
            <p class="mt-1 text-xs text-stone-500">{{ $totalEmployees }} total on record</p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">Today&apos;s Attendance</p>
            <p class="mt-2 text-3xl font-semibold text-stone-900">{{ $today['present_count'] }} present</p>
            <p class="mt-1 text-xs text-stone-500">
                {{ $today['absent_count'] }} absent
                @if ($unmarkedToday > 0)
                    · {{ $unmarkedToday }} not marked
                @endif
            </p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">{{ $months[$month] ?? $month }} Payroll</p>
            <p class="mt-2 text-3xl font-semibold text-stone-900">{{ number_format($payrollSummary->total_net_pay ?? 0, 0) }} MMK</p>
            <p class="mt-1 text-xs text-stone-500">
                {{ $payrollSummary->unpaid_count ?? 0 }} unpaid · {{ $payrollSummary->paid_count ?? 0 }} paid
            </p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">Cash Advances This Month</p>
            <p class="mt-2 text-3xl font-semibold text-stone-900">{{ number_format($monthAdvances->total_amount ?? 0, 0) }} MMK</p>
            <p class="mt-1 text-xs text-stone-500">{{ $monthAdvances->total_count ?? 0 }} advance(s) recorded</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white">
            <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-stone-900">Employees by Department</h2>
                <x-button href="{{ route('employees.create') }}" variant="secondary">Add Employee</x-button>
            </div>

            <div class="divide-y divide-stone-100">
                @forelse ($departments as $department)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="font-medium text-stone-900">{{ $department->name }}</p>
                            <p class="text-sm text-stone-500">{{ $department->employees_count }} employees</p>
                        </div>
                        <a href="{{ route('employees.index', ['department_id' => $department->id]) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            View
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

        <div class="rounded-xl border border-stone-200 bg-white">
            <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-stone-900">Unpaid Payrolls</h2>
                <x-button href="{{ route('payrolls.index') }}" variant="secondary">All Payrolls</x-button>
            </div>

            <div class="divide-y divide-stone-100">
                @forelse ($unpaidPayrolls as $payroll)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="font-medium text-stone-900">{{ $payroll->employee->name }}</p>
                            <p class="text-sm text-stone-500">
                                {{ $months[$payroll->month] ?? $payroll->month }} {{ $payroll->year }}
                                · {{ number_format($payroll->net_pay, 0) }} MMK
                            </p>
                        </div>
                        <a href="{{ route('payrolls.show', $payroll) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            Review
                        </a>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-stone-500">
                        No unpaid payrolls waiting for payment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
