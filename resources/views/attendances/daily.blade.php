@extends('layouts.app')

@section('title', 'Daily Attendance')

@section('content')
    @php
        use App\Enums\AttendanceStatus;
        use App\Enums\WorkType;
    @endphp

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Daily Attendance</h1>
            <p class="mt-1 text-sm text-stone-500">Mark who came to work and what they did</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-button href="{{ route('attendances.daily', ['date' => $previousDate]) }}" variant="secondary">Previous day</x-button>
            <x-button href="{{ route('attendances.daily', ['date' => $nextDate]) }}" variant="secondary">Next day</x-button>
            <x-button href="{{ route('attendances.index') }}" variant="secondary">View history</x-button>
        </div>
    </div>

    <form method="GET" action="{{ route('attendances.daily') }}" class="mb-6 flex flex-col gap-4 rounded-xl border border-stone-200 bg-white p-4 sm:flex-row sm:items-end">
        <x-form-input name="date" label="Attendance date" type="date" :value="$date->toDateString()" class="sm:max-w-xs" />
        <x-button type="submit" variant="secondary">Go to date</x-button>
    </form>

    @if ($employees->isEmpty())
        <div class="rounded-xl border border-stone-200 bg-white p-8 text-center text-sm text-stone-500">
            No active employees found for this date. Add employees first or choose another date.
        </div>
    @else
        <form method="POST" action="{{ route('attendances.daily.store') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $date->toDateString() }}">

            <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
                <div class="border-b border-stone-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-stone-900">{{ $date->format('l, d M Y') }}</h2>
                    <p class="text-sm text-stone-500">{{ $employees->count() }} active {{ str('employee')->plural($employees->count()) }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Department</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Hours</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Minutes</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Work type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($employees as $index => $employee)
                                @php
                                    $record = $existingAttendances->get($employee->id);
                                    $status = old("attendances.{$index}.status", $record?->status->value ?? AttendanceStatus::Present->value);
                                    $hours = old("attendances.{$index}.hours_worked", $record?->hours_worked ?? 8);
                                    $minutes = old("attendances.{$index}.minutes_worked", $record?->minutes_worked ?? 0);
                                    $workType = old("attendances.{$index}.work_type", $record?->work_type?->value);
                                    $notes = old("attendances.{$index}.notes", $record?->notes);
                                @endphp
                                <tr>
                                    <td class="px-4 py-4 sm:px-6">
                                        <input type="hidden" name="attendances[{{ $index }}][employee_id]" value="{{ $employee->id }}">
                                        <span class="font-medium text-stone-900">{{ $employee->name }}</span>
                                        <p class="text-xs text-stone-500">{{ $employee->position ?? 'No position' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-stone-600 sm:px-6">{{ $employee->department?->name ?? '—' }}</td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <select
                                            name="attendances[{{ $index }}][status]"
                                            class="attendance-status block w-full min-w-28 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                        >
                                            @foreach (AttendanceStatus::options() as $value => $label)
                                                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <input
                                            type="number"
                                            name="attendances[{{ $index }}][hours_worked]"
                                            value="{{ $hours }}"
                                            min="0"
                                            max="24"
                                            class="attendance-hours block w-full min-w-20 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                        >
                                    </td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <input
                                            type="number"
                                            name="attendances[{{ $index }}][minutes_worked]"
                                            value="{{ $minutes }}"
                                            min="0"
                                            max="59"
                                            class="attendance-minutes block w-full min-w-20 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                        >
                                    </td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <select
                                            name="attendances[{{ $index }}][work_type]"
                                            class="attendance-work-type block w-full min-w-36 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                        >
                                            <option value="">Select work type</option>
                                            @foreach (WorkType::options() as $value => $label)
                                                <option value="{{ $value }}" @selected($workType === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4 sm:px-6">
                                        <input
                                            type="text"
                                            name="attendances[{{ $index }}][notes]"
                                            value="{{ $notes }}"
                                            placeholder="Optional"
                                            class="block w-full min-w-40 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="submit">Save attendance</x-button>
            </div>
        </form>
    @endif

    @push('scripts')
        <script>
            document.querySelectorAll('.attendance-status').forEach((select) => {
                const row = select.closest('tr');

                const toggleRow = () => {
                    const isPresent = select.value === 'present';
                    row.querySelectorAll('.attendance-hours, .attendance-minutes, .attendance-work-type').forEach((field) => {
                        field.disabled = !isPresent;
                        field.closest('td').classList.toggle('opacity-50', !isPresent);
                    });
                };

                select.addEventListener('change', toggleRow);
                toggleRow();
            });
        </script>
    @endpush
@endsection
