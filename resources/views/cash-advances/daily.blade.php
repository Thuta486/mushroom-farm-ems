@extends('layouts.app')

@section('title', __('app.cash_advances.daily_cash_advances'))

@section('content')
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.cash_advances.daily_cash_advances') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.cash_advances.daily_cash_advances_subtitle') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-button href="{{ route('cash-advances.daily', ['date' => $previousDate]) }}" variant="secondary">{{ __('app.common.previous_day') }}</x-button>
            <x-button href="{{ route('cash-advances.daily', ['date' => $nextDate]) }}" variant="secondary">{{ __('app.common.next_day') }}</x-button>
            <x-button href="{{ route('cash-advances.index') }}" variant="secondary">{{ __('app.common.view_history') }}</x-button>
        </div>
    </div>

    <form method="GET" action="{{ route('cash-advances.daily') }}" class="mb-6 flex flex-col gap-4 rounded-xl border border-stone-200 bg-white p-4 sm:flex-row sm:items-end">
        <x-form-input name="date" label="{{ __('app.common.date') }}" type="date" :value="$date->toDateString()" class="sm:max-w-xs" />
        <x-button type="submit" variant="secondary">{{ __('app.common.go_to_date') }}</x-button>
    </form>

    @if ($employees->isEmpty())
        <div class="rounded-xl border border-stone-200 bg-white p-8 text-center text-sm text-stone-500">
            {{ __('app.cash_advances.no_active_employees') }}
        </div>
    @elseif ($advanceTypes->isEmpty())
        <div class="rounded-xl border border-stone-200 bg-white p-8 text-center text-sm text-stone-500">
            {{ __('app.cash_advances.no_advance_types_set_up') }}
        </div>
    @else
        @php
            // Seed the form with existing rows for the day, or one blank row if there's nothing yet.
            $seedRows = $existingAdvances->isEmpty()
                ? [['employee_id' => '', 'advance_type_id' => $advanceTypes->keys()->first(), 'amount' => '', 'notes' => '']]
                : $existingAdvances->map(fn ($advance) => [
                    'employee_id' => $advance->employee_id,
                    'advance_type_id' => $advance->advance_type_id,
                    'amount' => $advance->amount,
                    'notes' => $advance->notes,
                ])->values()->all();
        @endphp

        <form method="POST" action="{{ route('cash-advances.daily.store') }}" id="daily-advance-form">
            @csrf
            <input type="hidden" name="date" value="{{ $date->toDateString() }}">

            <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
                <div class="border-b border-stone-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-stone-900">{{ $date->format('l, d M Y') }}</h2>
                    <p class="text-sm text-stone-500">{{ __('app.cash_advances.daily_cash_advances_table_instructions') }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">{{ __('app.common.employee') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">{{ __('app.common.amount') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">{{ __('app.common.type') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">{{ __('app.common.notes') }}</th>
                                <th class="px-4 py-3 sm:px-6"></th>
                            </tr>
                        </thead>
                        <tbody id="advance-rows" class="divide-y divide-stone-100"></tbody>
                    </table>
                </div>

                <div class="border-t border-stone-200 px-6 py-4">
                    <x-button type="button" id="add-row" variant="secondary">+ {{ __('app.cash_advances.add_row') }}</x-button>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="submit">{{ __('app.cash_advances.save_cash_advances') }}</x-button>
            </div>
        </form>
    @endif

    @push('scripts')
        <script>
            (function () {
                const employees = @json($employees->map(fn ($e) => ['id' => $e->id, 'name' => $e->display_name])->values());
                const advanceTypes = @json($advanceTypes);
                const seedRows = @json($seedRows ?? []);
                const tbody = document.getElementById('advance-rows');
                let rowIndex = 0;

                function employeeOptions(selected) {
                    return employees.map((e) => `<option value="${e.id}" ${String(e.id) === String(selected) ? 'selected' : ''}>${e.name}</option>`).join('');
                }

                function typeOptions(selected) {
                    return Object.entries(advanceTypes).map(([id, name]) => `<option value="${id}" ${String(id) === String(selected) ? 'selected' : ''}>${name}</option>`).join('');
                }

                function addRow(data) {
                    data = data || { employee_id: '', advance_type_id: '', amount: '', notes: '' };
                    const index = rowIndex++;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-4 py-4 sm:px-6">
                            <select name="advances[${index}][employee_id]" required class="block w-full min-w-40 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <option value="">{{ __('app.common.select_employee') }}</option>
                                ${employeeOptions(data.employee_id)}
                            </select>
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <input type="number" name="advances[${index}][amount]" value="${data.amount ?? ''}" min="0" step="0.01" placeholder="0" class="block w-full min-w-28 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <select name="advances[${index}][advance_type_id]" class="block w-full min-w-36 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                ${typeOptions(data.advance_type_id)}
                            </select>
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <input type="text" name="advances[${index}][notes]" value="${data.notes ?? ''}" class="block w-full min-w-40 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <button type="button" class="remove-row font-medium text-red-600 hover:text-red-700">{{ __('app.common.remove') }}</button>
                        </td>
                    `;
                    tr.querySelector('.remove-row').addEventListener('click', () => tr.remove());
                    tbody.appendChild(tr);
                }

                seedRows.forEach((row) => addRow(row));

                document.getElementById('add-row').addEventListener('click', () => addRow());
            })();
        </script>
    @endpush
@endsection