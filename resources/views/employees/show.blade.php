@extends('layouts.app')

@section('title', $employee->name)

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-stone-900">{{ $employee->name }}</h1>
                <x-status-badge :status="$employee->employment_status->value" />
            </div>
            <p class="mt-1 text-sm text-stone-500">{{ $employee->position ?? __('app.employees.no_position_set') }} · {{ $employee->department?->name ?? __('app.employees.no_department') }}</p>
        </div>

        <div class="flex gap-2">
            <x-button href="{{ route('employees.edit', $employee) }}" variant="secondary">{{ __('app.common.edit') }}</x-button>
            <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('{{ __('app.employees.remove_or_terminate_employee_confirmation') }}')">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">{{ __('app.common.remove') }}</x-button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.employees.personal_details') }}</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.common.phone') }}</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.employees.gender') }}</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->gender ? ucfirst($employee->gender) : '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.employees.date_of_birth') }}</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.employees.joining_date') }}</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->joining_date->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.employees.emergency_contact') }}</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->emergency_contact ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-stone-500">{{ __('app.employees.address') }}</dt>
                    <dd class="mt-1 font-medium text-stone-900">{{ $employee->address ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.employees.employment') }}</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.employees.monthly_wage') }}</dt>
                    <dd class="font-medium text-stone-900">{{ number_format($employee->wage_amount, 0) }} {{ __('app.common.currency') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">{{ __('app.employees.holiday_allowance') }}</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->holiday?->allowed_days_per_month ?? 2 }} {{ __('app.common.days_per_month') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-stone-200 bg-white">
        <div class="border-b border-stone-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.employees.salary_history') }}</h2>
            <p class="text-sm text-stone-500">{{ __('app.employees.recent_wage_changes') }}</p>
        </div>
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.employees.effective_date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.employees.wage') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.reason') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employee->salaryHistories as $history)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $history->effective_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-stone-900">{{ number_format($history->wage_amount, 0) }} MMK</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $history->reason ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-stone-500">{{ __('app.employees.no_salary_history') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 rounded-xl border border-stone-200 bg-white">
        <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-stone-900">{{ __('app.employees.recent_attendance') }}</h2>
                <p class="text-sm text-stone-500">{{ __('app.employees.recent_attendance_subtitle') }}</p>
            </div>
            <a href="{{ route('attendances.index', ['employee_id' => $employee->id]) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                {{ __('app.employees.view_all') }}
            </a>
        </div>

        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.attendance.time_worked') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.attendance.work_type') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employee->attendances as $attendance)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $attendance->date->format('d M Y') }}</td>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-stone-500">{{ __('app.employees.no_attendance_records_yet') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
