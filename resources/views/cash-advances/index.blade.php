@extends('layouts.app')

@section('title', __('app.cash_advances.title'))

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.cash_advances.title') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.cash_advances.subtitle') }}</p>
        </div>
        <div class="flex gap-2">
            <x-button href="{{ route('cash-advances.daily') }}">{{ __('app.cash_advances.daily_entry') }}</x-button>
            <x-button href="{{ route('cash-advances.create') }}" variant="secondary">{{ __('app.cash_advances.record_single_advance') }}</x-button>
        </div>
    </div>

    <form method="GET" action="{{ route('cash-advances.index') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-2 lg:grid-cols-4">
        <x-form-input name="date_from" label="{{ __('app.common.from_date') }}" type="date" :value="request('date_from')" />
        <x-form-input name="date_to" label="{{ __('app.common.to_date') }}" type="date" :value="request('date_to')" />
        <x-form-select name="employee_id" label="{{ __('app.common.employee') }}" :options="$employees" :selected="request('employee_id')" placeholder="{{ __('app.common.all_employees') }}" />
        <div class="flex items-end gap-2">
            <x-button type="submit" class="w-full">{{ __('app.common.filter') }}</x-button>
            <x-button href="{{ route('cash-advances.index') }}" variant="secondary" class="w-full">{{ __('app.common.reset') }}</x-button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.employee') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.notes') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($cashAdvances as $cashAdvance)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $cashAdvance->date->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('employees.show', $cashAdvance->employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">
                                {{ $cashAdvance->employee->display_name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $cashAdvance->advanceType->display_name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($cashAdvance->amount, 0) }} {{ __('app.cash_advances.currency') }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $cashAdvance->notes ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('cash-advances.edit', $cashAdvance) }}" class="font-medium text-emerald-700 hover:text-emerald-800">{{ __('app.common.edit') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.cash_advances.no_cash_advances_recorded_yet') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($cashAdvances->hasPages())
        <div class="mt-6">
            {{ $cashAdvances->links() }}
        </div>
    @endif
@endsection