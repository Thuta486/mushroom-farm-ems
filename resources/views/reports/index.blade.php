@extends('layouts.app')

@section('title', __('app.reports.title'))

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.reports.title') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.reports.subtitle') }}</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <a href="{{ route('reports.attendance') }}" class="group rounded-xl border border-stone-200 bg-white p-6 transition hover:border-emerald-300 hover:shadow-sm">
            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.reports.attendance_report') }}</h2>
            <p class="mt-2 text-sm text-stone-500">{{ __('app.reports.attendance_report_description') }}</p>
        </a>

        <a href="{{ route('reports.payroll') }}" class="group rounded-xl border border-stone-200 bg-white p-6 transition hover:border-emerald-300 hover:shadow-sm">
            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.payrolls.payroll_summary') }}</h2>
            <p class="mt-2 text-sm text-stone-500">{{ __('app.reports.payroll_summary_description') }}</p>
        </a>

        <a href="{{ route('reports.cash-advances') }}" class="group rounded-xl border border-stone-200 bg-white p-6 transition hover:border-emerald-300 hover:shadow-sm">
            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 group-hover:bg-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-stone-900">{{ __('app.reports.cash_advance_report') }}</h2>
            <p class="mt-2 text-sm text-stone-500">{{ __('app.reports.cash_advance_report_description') }}</p>
        </a>
    </div>
@endsection
