@extends('layouts.app')

@section('title', 'Payroll Details')

@section('content')
    @php
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
        $isPaid = $payroll->status->value === 'paid';
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-stone-900">{{ $payroll->employee->name }}</h1>
                <x-status-badge :status="$payroll->status->value" />
            </div>
            <p class="mt-1 text-sm text-stone-500">
                {{ $months[$payroll->month] ?? $payroll->month }} {{ $payroll->year }}
                · {{ $payroll->employee->department?->name ?? 'No department' }}
            </p>
        </div>

        <div class="flex gap-2">
            @unless ($isPaid)
                <form method="POST" action="{{ route('payrolls.mark-paid', $payroll) }}" onsubmit="return confirm('Mark this payroll as paid?')">
                    @csrf
                    <x-button type="submit">Mark as Paid</x-button>
                </form>
            @endunless
            <x-button href="{{ route('payrolls.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}" variant="secondary">Back to List</x-button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-stone-900">Attendance Summary</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Days Worked</dt>
                    <dd class="font-medium text-stone-900">{{ $payroll->total_worked_days }} days</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Hours Worked</dt>
                    <dd class="font-medium text-stone-900">{{ $payroll->total_worked_hours }}h {{ str_pad((string) $payroll->total_worked_minutes, 2, '0', STR_PAD_LEFT) }}m</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Monthly Wage</dt>
                    <dd class="font-medium text-stone-900">{{ number_format($payroll->employee->wage_amount, 0) }} MMK</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-stone-900">Pay Breakdown</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Gross Salary</dt>
                    <dd class="font-medium text-stone-900">{{ number_format($payroll->gross_salary, 0) }} MMK</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-emerald-600">Bonuses</dt>
                    <dd class="font-medium text-emerald-700">+ {{ number_format($payroll->total_bonus, 0) }} MMK</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-red-600">Deductions</dt>
                    <dd class="font-medium text-red-700">− {{ number_format($payroll->total_deduction, 0) }} MMK</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-red-600">Cash Advances</dt>
                    <dd class="font-medium text-red-700">− {{ number_format($payroll->total_advances, 0) }} MMK</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-stone-200 pt-3">
                    <dt class="font-semibold text-stone-900">Net Pay</dt>
                    <dd class="text-lg font-semibold text-stone-900">{{ number_format($payroll->net_pay, 0) }} MMK</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-stone-200 bg-white">
        <div class="border-b border-stone-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-stone-900">Adjustments</h2>
            <p class="text-sm text-stone-500">Bonuses and deductions for this payroll</p>
        </div>

        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Reason</th>
                    @unless ($isPaid)
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">Actions</th>
                    @endunless
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($payroll->payrollAdjustments as $adjustment)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-900">{{ $adjustment->adjustmentType->name }}</td>
                        <td class="px-6 py-4 text-sm capitalize text-stone-600">{{ $adjustment->adjustmentType->category }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($adjustment->amount, 0) }} MMK</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $adjustment->reason ?? '—' }}</td>
                        @unless ($isPaid)
                            <td class="px-6 py-4 text-right text-sm">
                                <form method="POST" action="{{ route('payrolls.adjustments.destroy', [$payroll, $adjustment]) }}" class="inline" onsubmit="return confirm('Remove this adjustment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-700">Remove</button>
                                </form>
                            </td>
                        @endunless
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isPaid ? 4 : 5 }}" class="px-6 py-8 text-center text-sm text-stone-500">No adjustments yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @unless ($isPaid)
            <div class="border-t border-stone-200 px-6 py-4">
                <form method="POST" action="{{ route('payrolls.adjustments.store', $payroll) }}" class="grid gap-4 md:grid-cols-4">
                    @csrf
                    <x-form-select
                        name="adjustment_type_id"
                        label="Type"
                        :options="$adjustmentTypes->pluck('name', 'id')"
                        placeholder="Select type"
                    />
                    <x-form-input name="amount" label="Amount (MMK)" type="number" step="0.01" min="0.01" />
                    <x-form-input name="reason" label="Reason" :value="old('reason')" />
                    <div class="flex items-end">
                        <x-button type="submit" class="w-full">Add Adjustment</x-button>
                    </div>
                </form>
            </div>
        @endunless
    </div>
@endsection
