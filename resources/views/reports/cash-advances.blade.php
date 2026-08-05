@extends('layouts.app')

@section('title', __('app.reports.cash_advance_report'))

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.reports.cash_advance_report') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.reports.cash_advance_report_description') }}</p>
        </div>
        <x-button href="{{ route('reports.index') }}" variant="secondary">{{ __('app.reports.all_reports') }}</x-button>
    </div>

    <form method="GET" action="{{ route('reports.cash-advances') }}"
        class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-2 lg:grid-cols-5">
        <x-form-input name="date_from" label="{{ __('app.attendance.from_date') }}" type="date" :value="$dateFrom->toDateString()" />
        <x-form-input name="date_to" label="{{ __('app.attendance.to_date') }}" type="date" :value="$dateTo->toDateString()" />
        <x-form-select name="employee_id" label="{{ __('app.common.employee') }}" :options="$employees" :selected="request('employee_id')"
            placeholder="{{ __('app.common.all_employees') }}" />
        <div class="flex items-end gap-2 md:col-span-2">
            <x-button type="submit" class="w-full">{{ __('app.reports.filter') }}</x-button>
            <x-button href="{{ route('reports.cash-advances') }}" variant="secondary"
                class="w-full">{{ __('app.common.reset') }}</x-button>
        </div>
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.reports.total_advances') }}</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ $report['summary']->total_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.reports.total_amount') }}</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-700">
                {{ number_format($report['summary']->total_amount ?? 0, 0) }} {{ __('app.common.currency') }}</p>
        </div>
    </div>
    <div class="border-b border-stone-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-stone-900">{{ __('app.reports.by_employee') }}</h2>
    </div>
    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="min-w-[900px] w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.common.employee') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.common.department') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.cash_advances.advances') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.common.total_amount') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($report['employeeRows'] as $row)
                    <tr>
                        <td class="px-6 py-4 font-medium text-stone-900">{{ $row->employee_name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">
                            {{ $row->department_name ?? __('app.employees.no_department') }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $row->advance_count }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($row->total_amount, 0) }}
                            {{ __('app.common.currency') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.cash_advances.no_cash_advances_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-b border-stone-200 px-6 py-4">
        <h2 class="text-lg font-semibold text-stone-900">{{ __('app.reports.all_records') }}</h2>
    </div>
    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="min-w-[900px] w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class=" px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.common.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.common.employee') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.common.type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.common.amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">
                        {{ __('app.common.notes') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($report['rows'] as $advance)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $advance->date->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-stone-900">{{ $advance->employee->display_name }}</p>
                            <p class="text-xs text-stone-500">
                                {{ $advance->employee->department?->display_name ?? __('app.employees.no_department') }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $advance->advanceType->display_name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($advance->amount, 0) }}
                            {{ __('app.common.currency') }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $advance->notes ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.cash_advances.no_cash_advances_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
