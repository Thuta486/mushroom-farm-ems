@extends('layouts.app')

@section('title', 'Cash Advance Report')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Cash Advance Report</h1>
            <p class="mt-1 text-sm text-stone-500">Advances given to workers during a period</p>
        </div>
        <x-button href="{{ route('reports.index') }}" variant="secondary">All Reports</x-button>
    </div>

    <form method="GET" action="{{ route('reports.cash-advances') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-2 lg:grid-cols-5">
        <x-form-input name="date_from" label="From date" type="date" :value="$dateFrom->toDateString()" />
        <x-form-input name="date_to" label="To date" type="date" :value="$dateTo->toDateString()" />
        <x-form-select name="employee_id" label="Employee" :options="$employees" :selected="request('employee_id')" placeholder="All employees" />
        <div class="flex items-end gap-2 md:col-span-2">
            <x-button type="submit" class="w-full">Run Report</x-button>
            <x-button href="{{ route('reports.cash-advances') }}" variant="secondary" class="w-full">Reset</x-button>
        </div>
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Total Advances</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ $report['summary']->total_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Total Amount</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ number_format($report['summary']->total_amount ?? 0, 0) }} MMK</p>
        </div>
    </div>

    <div class="mb-8 overflow-hidden rounded-xl border border-stone-200 bg-white">
        <div class="border-b border-stone-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-stone-900">By Employee</h2>
        </div>
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Advances</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Total Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($report['employeeRows'] as $row)
                    <tr>
                        <td class="px-6 py-4 font-medium text-stone-900">{{ $row->employee_name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $row->department_name ?? 'No department' }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $row->advance_count }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($row->total_amount, 0) }} MMK</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-stone-500">
                            No cash advances found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <div class="border-b border-stone-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-stone-900">All Records</h2>
        </div>
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($report['rows'] as $advance)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $advance->date->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-stone-900">{{ $advance->employee->name }}</p>
                            <p class="text-xs text-stone-500">{{ $advance->employee->department?->name ?? 'No department' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $advance->advanceType->name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($advance->amount, 0) }} MMK</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $advance->notes ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-stone-500">
                            No cash advances found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
