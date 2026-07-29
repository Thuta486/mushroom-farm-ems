@extends('layouts.app')

@section('title', 'Cash Advances')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Cash Advances</h1>
            <p class="mt-1 text-sm text-stone-500">Track money given to employees before payday</p>
        </div>
        <x-button href="{{ route('cash-advances.create') }}">Record Advance</x-button>
    </div>

    <form method="GET" action="{{ route('cash-advances.index') }}" class="mb-6 grid gap-4 rounded-xl border border-stone-200 bg-white p-4 md:grid-cols-2 lg:grid-cols-4">
        <x-form-input name="date_from" label="From date" type="date" :value="request('date_from')" />
        <x-form-input name="date_to" label="To date" type="date" :value="request('date_to')" />
        <x-form-select name="employee_id" label="Employee" :options="$employees" :selected="request('employee_id')" placeholder="All employees" />
        <div class="flex items-end gap-2">
            <x-button type="submit" class="w-full">Filter</x-button>
            <x-button href="{{ route('cash-advances.index') }}" variant="secondary" class="w-full">Reset</x-button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Notes</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($cashAdvances as $cashAdvance)
                    <tr>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $cashAdvance->date->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('employees.show', $cashAdvance->employee) }}" class="font-medium text-emerald-700 hover:text-emerald-800">
                                {{ $cashAdvance->employee->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $cashAdvance->advanceType->name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ number_format($cashAdvance->amount, 0) }} MMK</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $cashAdvance->notes ?? '—' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('cash-advances.edit', $cashAdvance) }}" class="font-medium text-emerald-700 hover:text-emerald-800">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-stone-500">
                            No cash advances recorded yet.
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
