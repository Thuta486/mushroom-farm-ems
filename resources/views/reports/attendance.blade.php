@extends('layouts.app')

@section('title', __('app.reports.attendance_report'))

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.reports.attendance_report') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.reports.attendance_report_description') }}</p>
        </div>
        <x-button href="{{ route('reports.index') }}" variant="secondary">{{ __('app.reports.all_reports') }}</x-button>
    </div>

    <form method="GET" action="{{ route('reports.attendance') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-3 lg:grid-cols-6">
        <x-form-input name="date_from" label="{{ __('app.common.from_date') }}" type="date" :value="$dateFrom->toDateString()" />
        <x-form-input name="date_to" label="{{ __('app.common.to_date') }}" type="date" :value="$dateTo->toDateString()" />
        <x-form-select name="department_id" label="{{ __('app.common.department') }}" :options="$departments" :selected="request('department_id')" placeholder="{{ __('app.common.all_departments') }}" />
        <x-form-select name="employee_id" label="{{ __('app.common.employee') }}" :options="$employees" :selected="request('employee_id')" placeholder="{{ __('app.common.all_employees') }}" />
        <div class="flex items-end gap-2 md:col-span-2">
            <x-button type="submit" class="w-full">{{ __('app.reports.filter') }}</x-button>
            <x-button href="{{ route('reports.attendance') }}" variant="secondary" class="w-full">{{ __('app.common.reset') }}</x-button>
        </div>
    </form>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.reports.total_records') }}</p>
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ $report['summary']->total_records ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.reports.present_days') }}</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-700">{{ $report['summary']->present_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.reports.absent_days') }}</p>
            <p class="mt-1 text-2xl font-semibold text-red-600">{{ $report['summary']->absent_count ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">{{ __('app.reports.total_hours_worked') }}</p>
            @php
                $totalMinutes = (($report['summary']->total_hours ?? 0) * 60) + ($report['summary']->total_minutes ?? 0);
                $hours = intdiv($totalMinutes, 60);
                $minutes = $totalMinutes % 60;
            @endphp
            <p class="mt-1 text-2xl font-semibold text-stone-900">{{ $hours }}h {{ $minutes }}m</p>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-[900px] w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.employee') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.department') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.reports.days_marked') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.reports.present_days') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.reports.absent_days') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.reports.total_hours_worked') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($report['rows'] as $row)
                    @php
                        $rowMinutes = (($row->total_hours ?? 0) * 60) + ($row->total_minutes ?? 0);
                        $rowHours = intdiv($rowMinutes, 60);
                        $rowMins = $rowMinutes % 60;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 font-medium text-stone-900">{{ $row->employee_name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $row->department_name ?? __('app.common.no_department') }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $row->days_marked }}</td>
                        <td class="px-6 py-4 text-sm text-emerald-700">{{ $row->present_days }}</td>
                        <td class="px-6 py-4 text-sm text-red-600">{{ $row->absent_days }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $rowHours }}h {{ $rowMins }}m</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.reports.no_attendance_records') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection