@extends('layouts.app')

@section('title', 'Payroll Summary Report')

@section('content')
    @php
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Payroll Summary</h1>
            <p class="mt-1 text-sm text-stone-500">Monthly payroll totals by department</p>
        </div>
        <x-button href="{{ route('reports.index') }}" variant="secondary">All Reports</x-button>
    </div>

    <form method="GET" action="{{ route('reports.payroll') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-2 lg:grid-cols-5">
        <x-form-select name="month" label="Month" :options="$months" :selected="$month" />
        <x-form-input name="year" label="Year" type="number" :value="$year" min="2020" max="2100" />
        <x-form-select name="department_id" label="Department" :options="$departments" :selected="request('department_id')" placeholder="All departments" />
        <div class="flex items-end gap-2 md:col-span-2">
            <x-button type="submit" class="w-full">Run Report</x-button>
            <x-button href="{{ route('reports.payroll') }}" variant="secondary" class="w-full">Reset</x-button>
        </div>
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Payroll Records</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ $report['summary']->total_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Total Gross</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ number_format($report['summary']->total_gross ?? 0, 0) }} MMK</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Total Advances Deducted</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ number_format($report['summary']->total_advances ?? 0, 0) }} MMK</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Total Net Pay</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ number_format($report['summary']->total_net_pay ?? 0, 0) }} MMK</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-[900px] w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Employees</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Gross</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Advances</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Net Pay</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Unpaid</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($report['departmentRows'] as $row)
                    <tr>
                        <td class="px-6 py-4 font-medium text-stone-900">{{ $row->department_name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $row->employee_count }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ number_format($row->total_gross, 0) }} MMK</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ number_format($row->total_advances, 0) }} MMK</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($row->total_net_pay, 0) }} MMK</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $row->unpaid_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-stone-500">
                            No payroll records found for {{ $months[$month] ?? $month }} {{ $year }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
