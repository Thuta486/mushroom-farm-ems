@extends('layouts.app')

@section('title', __('app.payrolls.title'))

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
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.payrolls.title') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.payrolls.subtitle') }}</p>
        </div>
        <x-button href="{{ route('payrolls.generate') }}">{{ __('app.payrolls.generate_payroll') }}</x-button>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.common.total_records') }}</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ $summary->total_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.payrolls.paid') }}</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ $summary->paid_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.payrolls.total_net_pay') }}</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ number_format($summary->total_net_pay ?? 0, 0) }} {{ __('app.common.currency') }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('payrolls.index') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-2 lg:grid-cols-5">
        <x-form-select name="month" label="{{ __('app.common.month') }}" :options="$months" :selected="$month" />
        <x-form-input name="year" label="{{ __('app.common.year') }}" type="number" :value="$year" min="2020" max="2100" />
        <x-form-select name="employee_id" label="{{ __('app.common.employee') }}" :options="$employees" :selected="request('employee_id')" placeholder="{{ __('app.common.all_employees') }}" />
        <x-form-select name="status" label="{{ __('app.common.status') }}" :options="PayrollStatus::options()" :selected="request('status')" placeholder="{{ __('app.payrolls.all_statuses') }}" />
        <div class="flex items-end gap-2">
            <x-button type="submit" class="w-full">{{ __('app.common.filter') }}</x-button>
            <x-button href="{{ route('payrolls.index') }}" variant="secondary" class="w-full">{{ __('app.common.reset') }}</x-button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-[900px] w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.employee') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.payrolls.period') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.payrolls.days_worked') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.payrolls.gross') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.payrolls.net_pay') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($payrolls as $payroll)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('employees.show', $payroll->employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">
                                {{ $payroll->employee->name }}
                            </a>
                            <p class="text-xs text-stone-500">{{ $payroll->employee->department?->name ?? __('app.employees.no_department') }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $months[$payroll->month] ?? $payroll->month }} {{ $payroll->year }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $payroll->total_worked_days }} {{ __('app.common.days') }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ number_format($payroll->gross_salary, 0) }} {{ __('app.common.currency') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($payroll->net_pay, 0) }} {{ __('app.common.currency') }}</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$payroll->status->value" />
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('payrolls.show', $payroll) }}" class="font-medium text-emerald-700 hover:text-emerald-800">{{ __('app.common.view') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.payrolls.no_records_yet') }}
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
