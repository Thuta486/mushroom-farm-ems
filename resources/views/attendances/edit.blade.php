@extends('layouts.app')

@section('title', __('app.attendance.edit_attendance'))

@section('content')
    @php
        use App\Enums\AttendanceStatus;
        use App\Enums\WorkType;
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.attendance.edit_attendance') }}</h1>
        <p class="mt-1 text-sm text-stone-500">
            {{ $attendance->employee->name }} · {{ $attendance->date->format('d M Y') }}
        </p>
    </div>

    <form method="POST" action="{{ route('attendances.update', $attendance) }}" class="max-w-xl space-y-6 rounded-xl border border-stone-200 bg-white p-6">
        @csrf
        @method('PUT')

        <x-form-select
            name="status"
            label="{{ __('app.attendance.status') }}"
            :options="AttendanceStatus::options()"
            :selected="old('status', $attendance->status->value)"
            required
        />

        <div class="grid gap-4 sm:grid-cols-2">
            <x-form-input
                name="hours_worked"
                label="{{ __('app.attendance.hours_worked') }}"
                type="number"
                :value="old('hours_worked', $attendance->hours_worked)"
                required
            />
            <x-form-input
                name="minutes_worked"
                label="{{ __('app.attendance.minutes_worked') }}"
                type="number"
                :value="old('minutes_worked', $attendance->minutes_worked)"
                required
            />
        </div>

        <x-form-select
            name="work_type"
            label="{{ __('app.attendance.work_type') }}"
            :options="WorkType::options()"
            :selected="old('work_type', $attendance->work_type?->value)"
            placeholder="{{ __('app.attendance.not_set') }}"
        />

        <x-form-textarea
            name="notes"
            label="{{ __('app.attendance.notes') }}"
            :value="old('notes', $attendance->notes)"
            rows="3"
        />

        <div class="flex flex-wrap gap-2">
            <x-button type="submit">{{ __('app.common.save_changes') }}</x-button>
            <x-button href="{{ route('attendances.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
        </div>
    </form>

    <form method="POST" action="{{ route('attendances.destroy', $attendance) }}" class="mt-4" onsubmit="return confirm('{{ __('app.attendance.remove_record_confirmation') }}')">
        @csrf
        @method('DELETE')
        <x-button type="submit" variant="danger">{{ __('app.attendance.remove_record') }}</x-button>
    </form>
@endsection
