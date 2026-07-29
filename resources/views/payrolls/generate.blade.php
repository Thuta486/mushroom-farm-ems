@extends('layouts.app')

@section('title', __('app.payrolls.generate_payroll'))

@section('content')
    @php
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.payrolls.generate_payroll') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.payrolls.generate_subtitle') }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <div class="mb-6 rounded-lg bg-stone-50 p-4 text-sm text-stone-600">
            <p class="font-medium text-stone-900">{{ __('app.reports.how_it_works') }}</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                <li>{{ __('app.payrolls.generate_how_it_works.fixed_salary') }}</li>
                <li>{{ __('app.payrolls.generate_how_it_works.absence_deduction') }}</li>
                <li>{{ __('app.payrolls.generate_how_it_works.adjustments') }}</li>
                <li>{{ __('app.payrolls.generate_how_it_works.deduct_advances') }}</li>
                <li>{{ __('app.payrolls.generate_how_it_works.skip_paid') }}</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('payrolls.store') }}" class="space-y-6">
            @csrf
            <x-form-select name="month" label="{{ __('app.common.month') }}" :options="$months" :selected="old('month', $month)" />
            <x-form-input name="year" label="{{ __('app.common.year') }}" type="number" :value="old('year', $year)" min="2020" max="2100" />

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.payrolls.generate_payroll') }}</x-button>
                <x-button href="{{ route('payrolls.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection
