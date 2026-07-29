@extends('layouts.app')

@section('title', 'Generate Payroll')

@section('content')
    @php
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Generate Payroll</h1>
        <p class="mt-1 text-sm text-stone-500">Calculate monthly pay from attendance, advances, and adjustments</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <div class="mb-6 rounded-lg bg-stone-50 p-4 text-sm text-stone-600">
            <p class="font-medium text-stone-900">How it works</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                <li>Gross salary is pro-rated from attendance and holiday allowance</li>
                <li>Cash advances for the month are deducted automatically</li>
                <li>Existing unpaid records are updated; paid records are skipped</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('payrolls.store') }}" class="space-y-6">
            @csrf
            <x-form-select name="month" label="Month" :options="$months" :selected="old('month', $month)" />
            <x-form-input name="year" label="Year" type="number" :value="old('year', $year)" min="2020" max="2100" />

            <div class="flex gap-3">
                <x-button type="submit">Generate Payroll</x-button>
                <x-button href="{{ route('payrolls.index') }}" variant="secondary">Cancel</x-button>
            </div>
        </form>
    </div>
@endsection
