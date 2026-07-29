@extends('layouts.app')

@section('title', __('app.cash_advances.record_cash_advance'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.cash_advances.record_cash_advance') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.cash_advances.record_cash_advance_subtitle') }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('cash-advances.store') }}" class="space-y-6">
            @csrf
            <x-form-select name="employee_id" label="{{ __('app.common.employee') }}" :options="$employees" :selected="old('employee_id')" placeholder="{{ __('app.common.select_employee') }}" />
            <x-form-select name="advance_type_id" label="{{ __('app.common.type') }}" :options="$advanceTypes" :selected="old('advance_type_id')" placeholder="{{ __('app.cash_advances.select_type') }}" />
            <x-form-input name="date" label="{{ __('app.common.date') }}" type="date" :value="old('date', now()->toDateString())" />
            <x-form-input name="amount" label="{{ __('app.cash_advances.amount_mmk') }}" type="number" step="0.01" min="0.01" :value="old('amount')" />
            <x-form-textarea name="notes" label="{{ __('app.common.notes') }}" rows="3">{{ old('notes') }}</x-form-textarea>

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.cash_advances.save_advance') }}</x-button>
                <x-button href="{{ route('cash-advances.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection
