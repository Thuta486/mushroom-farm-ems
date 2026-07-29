@extends('layouts.app')

@section('title', __('app.dashboard.title'))

@section('content')
    @php
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
        $unmarkedToday = max(0, $today['active_count'] - $today['marked_count']);
    @endphp

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.dashboard.title') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.dashboard.subtitle', ['date' => now()->format('d M Y')]) }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('attendances.daily') }}">{{ __('app.dashboard.mark_attendance') }}</x-button>
            <x-button href="{{ route('reports.index') }}" variant="secondary">{{ __('app.dashboard.view_reports') }}</x-button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">{{ __('app.dashboard.active_employees') }}</p>
            <p class="mt-2 text-3xl font-semibold text-emerald-700">{{ $activeEmployees }}</p>
            <p class="mt-1 text-xs text-stone-500">{{ __('app.dashboard.total_on_record', ['count' => $totalEmployees]) }}</p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">{{ __('app.dashboard.todays_attendance') }}</p>
            <p class="mt-2 text-3xl font-semibold text-stone-900">{{ $today['present_count'] }} {{ __('app.dashboard.present') }}</p>
            <p class="mt-1 text-xs text-stone-500">
                {{ $today['absent_count'] }} {{ __('app.dashboard.absent') }}
                @if ($unmarkedToday > 0)
                    · {{ $unmarkedToday }} {{ __('app.dashboard.not_marked') }}
                @endif
            </p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">{{ $months[$month] ?? $month }} {{ __('app.dashboard.payroll') }}</p>
            <p class="mt-2 text-3xl font-semibold text-stone-900">{{ number_format($payrollSummary->total_net_pay ?? 0, 0) }} MMK</p>
            <p class="mt-1 text-xs text-stone-500">
                {{ $payrollSummary->unpaid_count ?? 0 }} {{ __('app.dashboard.unpaid') }} · {{ $payrollSummary->paid_count ?? 0 }} {{ __('app.dashboard.paid') }}
            </p>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white p-6">
            <p class="text-sm font-medium text-stone-500">{{ __('app.dashboard.cash_advances_this_month') }}</p>
            <p class="mt-2 text-3xl font-semibold text-stone-900">{{ number_format($monthAdvances->total_amount ?? 0, 0) }} MMK</p>
            <p class="mt-1 text-xs text-stone-500">{{ $monthAdvances->total_count ?? 0 }} {{ __('app.dashboard.advances_recorded') }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-stone-200 bg-white">
            <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-stone-900">{{ __('app.dashboard.employees_by_department') }}</h2>
                <x-button href="{{ route('employees.create') }}" variant="secondary">{{ __('app.dashboard.add_employee') }}</x-button>
            </div>

            <div class="divide-y divide-stone-100">
                @forelse ($departments as $department)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="font-medium text-stone-900">{{ $department->name }}</p>
                            <p class="text-sm text-stone-500">{{ $department->employees_count }} employees</p>
                        </div>
                        <a href="{{ route('employees.index', ['department_id' => $department->id]) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            {{ __('app.dashboard.view') }}
                        </a>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-stone-500">
                        {{ __('app.dashboard.no_departments') }}
                        <a href="{{ route('departments.create') }}" class="font-medium text-emerald-700">{{ __('app.dashboard.add_a_department') }}</a>
                        {{ __('app.dashboard.to_get_started') }}
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-stone-200 bg-white">
            <div class="flex items-center justify-between border-b border-stone-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-stone-900">{{ __('app.dashboard.unpaid_payrolls') }}</h2>
                <x-button href="{{ route('payrolls.index') }}" variant="secondary">{{ __('app.dashboard.all_payrolls') }}</x-button>
            </div>

            <div class="divide-y divide-stone-100">
                @forelse ($unpaidPayrolls as $payroll)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <p class="font-medium text-stone-900">{{ $payroll->employee->name }}</p>
                            <p class="text-sm text-stone-500">
                                {{ $months[$payroll->month] ?? $payroll->month }} {{ $payroll->year }}
                                · {{ number_format($payroll->net_pay, 0) }} MMK
                            </p>
                        </div>
                        <a href="{{ route('payrolls.show', $payroll) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
                            {{ __('app.dashboard.view') }}
                        </a>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-sm text-stone-500">
                        No unpaid payrolls waiting for payment.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection