@extends('layouts.app')

@section('title', __('app.attendance.attendance_history'))

@section('content')
    @php
        use App\Enums\AttendanceStatus;
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.attendance.attendance_history') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.attendance.attendance_history_subtitle') }}</p>
        </div>
        <x-button href="{{ route('attendances.daily') }}">{{ __('app.attendance.mark_todays_attendance') }}</x-button>
    </div>

    <form method="GET" action="{{ route('attendances.index') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-3 lg:grid-cols-6">
        <x-form-input name="date_from" label="{{ __('app.attendance.from_date') }}" type="date" :value="request('date_from')" />
        <x-form-input name="date_to" label="{{ __('app.attendance.to_date') }}" type="date" :value="request('date_to')" />
        <x-form-select name="employee_id" label="{{ __('app.attendance.employee') }}" :options="$employees" :selected="request('employee_id')" placeholder="{{ __('app.attendance.all_employees') }}" />
        <x-form-select name="department_id" label="{{ __('app.attendance.department') }}" :options="$departments" :selected="request('department_id')" placeholder="{{ __('app.attendance.all_departments') }}" />
        <x-form-select name="status" label="{{ __('app.attendance.status') }}" :options="AttendanceStatus::options()" :selected="request('status')" placeholder="{{ __('app.attendance.all_statuses') }}" />
        <div class="flex items-end gap-2">
            <x-button type="submit" class="w-full">{{ __('app.attendance.filter') }}</x-button>
            <x-button href="{{ route('attendances.index') }}" variant="secondary" class="w-full">{{ __('app.common.reset') }}</x-button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.attendance.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.attendance.employee') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.attendance.department') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.attendance.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.attendance.time_worked') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.attendance.work_type') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($attendances as $attendance)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $attendance->date->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('employees.show', $attendance->employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">
                                {{ $attendance->employee->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $attendance->employee->department?->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$attendance->status->value" />
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">
                            @if ($attendance->status->value === 'present')
                                {{ $attendance->hours_worked }}h {{ str_pad((string) $attendance->minutes_worked, 2, '0', STR_PAD_LEFT) }}m
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $attendance->work_type?->label() ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('attendances.edit', $attendance) }}" class="font-medium text-emerald-700 hover:text-emerald-800">{{ __('app.common.edit') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.attendance.no_attendance_records_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($attendances->hasPages())
        <div class="mt-6">
            {{ $attendances->links() }}
        </div>
    @endif
@endsection
