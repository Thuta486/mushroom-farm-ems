@extends('layouts.app')

@section('title', 'Daily Cash Advances')

@section('content')
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Daily Cash Advances</h1>
            <p class="mt-1 text-sm text-stone-500">Record money given to workers today — add a row per advance, even more than one for the same worker</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-button href="{{ route('cash-advances.daily', ['date' => $previousDate]) }}" variant="secondary">Previous day</x-button>
            <x-button href="{{ route('cash-advances.daily', ['date' => $nextDate]) }}" variant="secondary">Next day</x-button>
            <x-button href="{{ route('cash-advances.index') }}" variant="secondary">View history</x-button>
        </div>
    </div>

    <form method="GET" action="{{ route('cash-advances.daily') }}" class="mb-6 flex flex-col gap-4 rounded-xl border border-stone-200 bg-white p-4 sm:flex-row sm:items-end">
        <x-form-input name="date" label="Date" type="date" :value="$date->toDateString()" class="sm:max-w-xs" />
        <x-button type="submit" variant="secondary">Go to date</x-button>
    </form>

    @if ($employees->isEmpty())
        <div class="rounded-xl border border-stone-200 bg-white p-8 text-center text-sm text-stone-500">
            No active employees found for this date. Add employees first or choose another date.
        </div>
    @elseif ($advanceTypes->isEmpty())
        <div class="rounded-xl border border-stone-200 bg-white p-8 text-center text-sm text-stone-500">
            No advance types set up yet. Add at least one advance type (e.g. Cash Advance) before recording advances.
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
                    <p class="text-sm text-stone-500">Add as many rows as needed — the same worker can appear more than once</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-stone-200">
                        <thead class="bg-stone-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500 sm:px-6">Notes</th>
                                <th class="px-4 py-3 sm:px-6"></th>
                            </tr>
                        </thead>
                        <tbody id="advance-rows" class="divide-y divide-stone-100"></tbody>
                    </table>
                </div>

                <div class="border-t border-stone-200 px-6 py-4">
                    <x-button type="button" id="add-row" variant="secondary">+ Add Row</x-button>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button type="submit">Save cash advances</x-button>
            </div>
        </form>
    @endif

    @push('scripts')
        <script>
            (function () {
                const employees = @json($employees->map(fn ($e) => ['id' => $e->id, 'name' => $e->name])->values());
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
                                <option value="">Select employee</option>
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
                            <input type="text" name="advances[${index}][notes]" value="${data.notes ?? ''}" placeholder="Optional" class="block w-full min-w-40 rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <button type="button" class="remove-row font-medium text-red-600 hover:text-red-700">Remove</button>
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