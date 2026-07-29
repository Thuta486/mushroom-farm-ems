@extends('layouts.app')

@section('title', 'Attendance History')

@section('content')
    @php
        use App\Enums\AttendanceStatus;
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Attendance History</h1>
            <p class="mt-1 text-sm text-stone-500">Review past attendance records and fix mistakes</p>
        </div>
        <x-button href="{{ route('attendances.daily') }}">Mark Today&apos;s Attendance</x-button>
    </div>

    <form method="GET" action="{{ route('attendances.index') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-3 lg:grid-cols-6">
        <x-form-input name="date_from" label="From date" type="date" :value="request('date_from')" />
        <x-form-input name="date_to" label="To date" type="date" :value="request('date_to')" />
        <x-form-select name="employee_id" label="Employee" :options="$employees" :selected="request('employee_id')" placeholder="All employees" />
        <x-form-select name="department_id" label="Department" :options="$departments" :selected="request('department_id')" placeholder="All departments" />
        <x-form-select name="status" label="Status" :options="AttendanceStatus::options()" :selected="request('status')" placeholder="All statuses" />
        <div class="flex items-end gap-2">
            <x-button type="submit" class="w-full">Filter</x-button>
            <x-button href="{{ route('attendances.index') }}" variant="secondary" class="w-full">Reset</x-button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="min-w-[900px] divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Time Worked</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Work Type</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">Actions</th>
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
                            <a href="{{ route('attendances.edit', $attendance) }}" class="font-medium text-emerald-700 hover:text-emerald-800">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-stone-500">
                            No attendance records found. Use the daily sheet to mark attendance.
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
