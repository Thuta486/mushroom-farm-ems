@extends('layouts.app')

@section('title', 'Record Cash Advance')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Record Cash Advance</h1>
        <p class="mt-1 text-sm text-stone-500">Log money given to an employee before monthly payroll</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('cash-advances.store') }}" class="space-y-6">
            @csrf
            <x-form-select name="employee_id" label="Employee" :options="$employees" :selected="old('employee_id')" placeholder="Select employee" />
            <x-form-select name="advance_type_id" label="Advance Type" :options="$advanceTypes" :selected="old('advance_type_id')" placeholder="Select type" />
            <x-form-input name="date" label="Date" type="date" :value="old('date', now()->toDateString())" />
            <x-form-input name="amount" label="Amount (MMK)" type="number" step="0.01" min="0.01" :value="old('amount')" />
            <x-form-textarea name="notes" label="Notes" rows="3">{{ old('notes') }}</x-form-textarea>

            <div class="flex gap-3">
                <x-button type="submit">Save Advance</x-button>
                <x-button href="{{ route('cash-advances.index') }}" variant="secondary">Cancel</x-button>
            </div>
        </form>
    </div>
@endsection
