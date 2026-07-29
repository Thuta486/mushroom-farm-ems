@extends('layouts.app')

@section('title', 'Edit Cash Advance')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Edit Cash Advance</h1>
        <p class="mt-1 text-sm text-stone-500">Update advance details for {{ $cashAdvance->employee->name }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('cash-advances.update', $cashAdvance) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <x-form-select name="employee_id" label="Employee" :options="$employees" :selected="old('employee_id', $cashAdvance->employee_id)" />
            <x-form-select name="advance_type_id" label="Advance Type" :options="$advanceTypes" :selected="old('advance_type_id', $cashAdvance->advance_type_id)" />
            <x-form-input name="date" label="Date" type="date" :value="old('date', $cashAdvance->date->toDateString())" />
            <x-form-input name="amount" label="Amount (MMK)" type="number" step="0.01" min="0.01" :value="old('amount', $cashAdvance->amount)" />
            <x-form-textarea name="notes" label="Notes" rows="3">{{ old('notes', $cashAdvance->notes) }}</x-form-textarea>

            <div class="flex gap-3">
                <x-button type="submit">Update Advance</x-button>
                <x-button href="{{ route('cash-advances.index') }}" variant="secondary">Cancel</x-button>
            </div>
        </form>

        <form method="POST" action="{{ route('cash-advances.destroy', $cashAdvance) }}" class="mt-6 border-t border-stone-200 pt-6" onsubmit="return confirm('Remove this cash advance?')">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger">Delete Advance</x-button>
        </form>
    </div>
@endsection
