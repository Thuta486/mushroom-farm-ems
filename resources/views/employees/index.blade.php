@extends('layouts.app')

@section('title', __('app.employees.title'))

@section('content')
    @php
        use App\Enums\EmploymentStatus;
    @endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.employees.title') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.employees.subtitle') }}</p>
        </div>
        <x-button href="{{ route('employees.create') }}">{{ __('app.employees.add_employee') }}</x-button>
    </div>

    <form method="GET" action="{{ route('employees.index') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-4">
        <x-form-input name="search" label="{{ __('app.employees.search_by_name') }}" :value="request('search')" />
        <x-form-select name="department_id" label="{{ __('app.departments.title') }}" :options="$departments" :selected="request('department_id')" placeholder="{{ __('app.employees.all_departments') }}" />
        <x-form-select name="employment_status" label="{{ __('app.employees.employment_status') }}" :options="['all' => __('app.employees.employment_status_placeholder')] + EmploymentStatus::options()" :selected="request('employment_status', 'active')" />
        <div class="flex items-end gap-2">
            <x-button type="submit" class="w-full">{{ __('app.employees.filter') }}</x-button>
            <x-button href="{{ route('employees.index') }}" variant="secondary" class="w-full">{{ __('app.employees.reset') }}</x-button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-[900px] w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.department') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.employees.position') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.employees.monthly_wage_mmk') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.employees.employment_status') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($employees as $employee)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('employees.show', $employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">
                                {{ $employee->display_name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $employee->department?->display_name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $employee->display_position ?: '—' }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ number_format($employee->wage_amount, 0) }} {{ __('app.common.currency') }}</td>
                        <td class="px-6 py-4">
                            <x-status-badge :status="$employee->employment_status->value" />
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('employees.edit', $employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">{{ __('app.common.edit') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.employees.no_employees_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($employees->hasPages())
        <div class="mt-6">
            {{ $employees->links() }}
        </div>
    @endif
@endsection
