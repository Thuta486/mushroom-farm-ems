@extends('layouts.app')

@section('title', __('app.payrolls.details'))

@section('content')
    @php
        $months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
        $isPaid = $payroll->status->value === 'paid';
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-stone-900">{{ $payroll->employee->display_name }}</h1>
                <x-status-badge :status="$payroll->status->value" />
            </div>
            <p class="mt-1 text-sm text-stone-500">
                {{ $months[$payroll->month] ?? $payroll->month }} {{ $payroll->year }}
                · {{ $payroll->employee->department?->display_name ?? __('app.employees.no_department') }}
            </p>
        </div>

        <div class="flex gap-2">
            @unless ($isPaid)
                <form method="POST" action="{{ route('payrolls.mark-paid', $payroll) }}"
                    onsubmit="return confirm('{{ __('app.payrolls.mark_as_paid_confirmation') }}')">
                    @csrf
                    <x-button type="submit">{{ __('app.payrolls.mark_as_paid') }}</x-button>
                </form>
            @else
                <form method="POST" action="{{ route('payrolls.mark-unpaid', $payroll) }}"
                    onsubmit="return confirm('{{ __('app.payrolls.mark_as_unpaid_confirmation') }}')">
                    @csrf
                    <x-button type="submit" variant="secondary">{{ __('app.payrolls.mark_as_unpaid') }}</x-button>
                </form>
            @endunless
            <x-button href="{{ route('payrolls.index', ['month' => $payroll->month, 'year' => $payroll->year]) }}"
                variant="secondary">{{ __('app.common.back_to_list') }}</x-button>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.payrolls.attendance_summary') }}</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.payrolls.present_days') }}</dt>
                    <dd class="font-medium text-stone-900">{{ $payroll->total_worked_days }} {{ __('app.common.days') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.payrolls.actual_work_days') }}</dt>
                    <dd class="font-medium text-stone-900">
                        @php
                            $totalMinutes = $payroll->total_worked_hours * 60 + $payroll->total_worked_minutes;

                            $actualDays = intdiv($totalMinutes, 8 * 60);
                            $remainingMinutes = $totalMinutes % (8 * 60);

                            $actualHours = intdiv($remainingMinutes, 60);
                            $actualMinutes = $remainingMinutes % 60;
                        @endphp

                        {{ $actualDays }}d {{ $actualHours }}h
                        {{ str_pad((string) $actualMinutes, 2, '0', STR_PAD_LEFT) }}m
                    </dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-stone-200 pt-3">
                    <dt class="text-red-600">{{ __('app.payrolls.absent_after_holiday_allowance') }}</dt>
                    <dd class="font-medium text-red-700">
                        {{ $payroll->total_absent_days }}d {{ $payroll->total_absent_hours }}h
                        {{ str_pad((string) $payroll->total_absent_minutes, 2, '0', STR_PAD_LEFT) }}m
                    </dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.employees.monthly_wage') }}</dt>
                    <dd class="font-medium text-stone-900">{{ number_format($payroll->employee->wage_amount, 0) }} {{ __('app.common.currency') }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.payrolls.pay_breakdown') }}</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.payrolls.gross_salary') }}</dt>
                    <dd class="font-medium text-stone-900">{{ number_format($payroll->gross_salary, 0) }} {{ __('app.common.currency') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-emerald-600">{{ __('app.payrolls.bonuses') }}</dt>
                    <dd class="font-medium text-emerald-700">+ {{ number_format($payroll->total_bonus, 0) }} {{ __('app.common.currency') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-red-600">{{ __('app.payrolls.deductions') }}</dt>
                    <dd class="font-medium text-red-700">− {{ number_format($payroll->total_deduction, 0) }} {{ __('app.common.currency') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-red-600">{{ __('app.payrolls.cash_advances') }}</dt>
                    <dd class="font-medium text-red-700">− {{ number_format($payroll->total_advances, 0) }} {{ __('app.common.currency') }}</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-stone-200 pt-3">
                    <dt class="font-semibold text-stone-900">{{ __('app.payrolls.net_pay') }}</dt>
                    <dd class="text-lg font-semibold text-stone-900">{{ number_format($payroll->net_pay, 0) }} {{ __('app.common.currency') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-stone-200 bg-white">
        <div class="border-b border-stone-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.payrolls.adjustments') }}</h2>
            <p class="text-sm text-stone-500">{{ __('app.payrolls.adjustments_subtitle') }}</p>
        </div>

        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.category') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.reason') }}</th>
                    @unless ($isPaid)
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.actions') }}</th>
                    @endunless
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($payroll->payrollAdjustments as $adjustment)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-900">{{ $adjustment->adjustmentType->display_name }}</td>
                        <td class="px-6 py-4 text-sm capitalize text-stone-600">{{ $adjustment->adjustmentType->category }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($adjustment->amount, 0) }} {{ __('app.common.currency') }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $adjustment->reason ?? '—' }}</td>
                        @unless ($isPaid)
                            <td class="px-6 py-4 text-right text-sm">
                                <form method="POST"
                                    action="{{ route('payrolls.adjustments.destroy', [$payroll, $adjustment]) }}"
                                    class="inline" onsubmit="return confirm('{{ __('app.payrolls.remove_adjustment_confirmation') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-700">{{ __('app.common.remove') }}</button>
                                </form>
                            </td>
                        @endunless
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isPaid ? 4 : 5 }}" class="px-6 py-8 text-center text-sm text-stone-500">{{ __('app.payrolls.no_adjustments_yet') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @unless ($isPaid)
            <div class="border-t border-stone-200 px-6 py-4">
                <form method="POST" action="{{ route('payrolls.adjustments.store', $payroll) }}"
                    class="grid gap-4 md:grid-cols-4">
                    @csrf
                    <x-form-select name="adjustment_type_id" label="{{ __('app.common.type') }}" :options="$adjustmentTypes->pluck('display_name', 'id')" placeholder="{{ __('app.cash_advances.select_type') }}" />
                    <x-form-input name="amount" label="{{ __('app.common.amount') }} ({{ __('app.common.currency') }})" type="number" step="0.01" min="0.01" />
                    <x-form-input name="reason" label="{{ __('app.common.reason') }}" :value="old('reason')" />
                    <div class="flex items-end">
                        <x-button type="submit" class="w-full">{{ __('app.payrolls.add_adjustment') }}</x-button>
                    </div>
                </form>
            </div>
        @endunless
    </div>
@endsection
