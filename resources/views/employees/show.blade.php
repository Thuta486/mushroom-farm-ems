@extends('layouts.app')

@section('title', $employee->name)

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-semibold text-stone-900">{{ $employee->name }}</h1>
                <x-status-badge :status="$employee->employment_status->value" />
            </div>
            <p class="mt-1 text-sm text-stone-500">{{ $employee->position ?? 'No position set' }} · {{ $employee->department?->name ?? 'No department' }}</p>
        </div>

        <div class="flex gap-2">
            <x-button href="{{ route('employees.edit', $employee) }}" variant="secondary">Edit</x-button>
            <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Remove or terminate this employee?')">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">Remove</x-button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-stone-900">Personal Details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Phone</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Gender</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->gender ? ucfirst($employee->gender) : '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Date of Birth</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Joining Date</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->joining_date->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Emergency Contact</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->emergency_contact ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-stone-500">Address</dt>
                    <dd class="mt-1 font-medium text-stone-900">{{ $employee->address ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="text-lg font-semibold text-stone-900">Employment</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Monthly Wage</dt>
                    <dd class="font-medium text-stone-900">{{ number_format($employee->wage_amount, 0) }} MMK</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-stone-500">Holiday Allowance</dt>
                    <dd class="font-medium text-stone-900">{{ $employee->holiday?->allowed_days_per_month ?? 2 }} days / month</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-stone-200 bg-white">
        <div class="border-b border-stone-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-stone-900">Salary History</h2>
            <p class="text-sm text-stone-500">Recent wage changes for this employee</p>
        </div>

        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Effective Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Wage</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Reason</th>
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
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-stone-500">No salary history yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 rounded-xl border border-stone-200 bg-white">
        <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-stone-900">Recent Attendance</h2>
                <p class="text-sm text-stone-500">Latest work days recorded for this employee</p>
            </div>
            <a href="{{ route('attendances.index', ['employee_id' => $employee->id]) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                View all
            </a>
        </div>

        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Time Worked</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Work Type</th>
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
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-stone-500">No attendance records yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
