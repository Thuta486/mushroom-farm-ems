@extends('layouts.app')

@section('title', 'Payrolls')

@section('content')
    @php
        use App\Enums\PayrollStatus;
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Payrolls</h1>
            <p class="mt-1 text-sm text-stone-500">Monthly salary records for your farm workers</p>
        </div>
        <x-button href="{{ route('payrolls.generate') }}">Generate Payroll</x-button>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Total Records</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ $summary->total_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Paid</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ $summary->paid_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Total Net Pay</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ number_format($summary->total_net_pay ?? 0, 0) }} MMK</p>
        </div>
    </div>

    <form method="GET" action="{{ route('payrolls.index') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-2 lg:grid-cols-5">
        <x-form-select name="month" label="Month" :options="$months" :selected="$month" />
        <x-form-input name="year" label="Year" type="number" :value="$year" min="2020" max="2100" />
        <x-form-select name="employee_id" label="Employee" :options="$employees" :selected="request('employee_id')" placeholder="All employees" />
        <x-form-select name="status" label="Status" :options="PayrollStatus::options()" :selected="request('status')" placeholder="All statuses" />
        <div class="flex items-end gap-2">
            <x-button type="submit" class="w-full">Filter</x-button>
            <x-button href="{{ route('payrolls.index') }}" variant="secondary" class="w-full">Reset</x-button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Days Worked</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Gross</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Net Pay</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($payrolls as $payroll)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('employees.show', $payroll->employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">
                                {{ $payroll->employee->name }}
                            </a>
                            <p class="text-xs text-stone-500">{{ $payroll->employee->department?->name ?? 'No department' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $months[$payroll->month] ?? $payroll->month }} {{ $payroll->year }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $payroll->total_worked_days }} days</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ number_format($payroll->gross_salary, 0) }} MMK</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($payroll->net_pay, 0) }} MMK</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$payroll->status->value" />
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('payrolls.show', $payroll) }}" class="font-medium text-emerald-700 hover:text-emerald-800">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-stone-500">
                            No payroll records for this period.
                            <a href="{{ route('payrolls.generate') }}" class="font-medium text-emerald-700">Generate payroll</a>
                            to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($payrolls->hasPages())
        <div class="mt-6">
            {{ $payrolls->links() }}
        </div>
    @endif
@endsection
