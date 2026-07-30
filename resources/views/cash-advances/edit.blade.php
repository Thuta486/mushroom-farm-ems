@extends('layouts.app')

@section('title', __('app.cash_advances.edit_cash_advance'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.cash_advances.edit_cash_advance') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.cash_advances.edit_cash_advance_subtitle', ['name' => $cashAdvance->employee->display_name]) }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('cash-advances.update', $cashAdvance) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <x-form-select name="employee_id" label="{{ __('app.common.employee') }}" :options="$employees" :selected="old('employee_id', $cashAdvance->employee_id)" />
            <x-form-select name="advance_type_id" label="{{ __('app.common.type') }}" :options="$advanceTypes" :selected="old('advance_type_id', $cashAdvance->advance_type_id)" />
            <x-form-input name="date" label="{{ __('app.common.date') }}" type="date" :value="old('date', $cashAdvance->date->toDateString())" />
            <x-form-input name="amount" label="{{ __('app.cash_advances.amount_mmk') }}" type="number" step="0.01" min="0.01" :value="old('amount', $cashAdvance->amount)" />
            <x-form-textarea name="notes" label="{{ __('app.common.notes') }}" rows="3">{{ old('notes', $cashAdvance->notes) }}</x-form-textarea>

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.cash_advances.update_advance') }}</x-button>
                <x-button href="{{ route('cash-advances.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>

        <form method="POST" action="{{ route('cash-advances.destroy', $cashAdvance) }}" class="mt-6 border-t border-stone-200 pt-6" onsubmit="return confirm('{{ __('app.cash_advances.remove_cash_advance_confirmation') }}')">
            @csrf
            @method('DELETE')
            <x-button type="submit" variant="danger">{{ __('app.cash_advances.delete_advance') }}</x-button>
        </form>
    </div>
@endsection
