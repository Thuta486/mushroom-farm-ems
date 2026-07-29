@extends('layouts.app')

@section('title', __('app.employees.edit_employee'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.employees.edit_employee') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.employees.update_employee_details', ['name' => $employee->name]) }}</p>
    </div>

    <div class="rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('employees._form-fields', ['employee' => $employee, 'departments' => $departments])
        </form>
    </div>
@endsection
