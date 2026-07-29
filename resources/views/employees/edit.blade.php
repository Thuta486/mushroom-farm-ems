@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Edit Employee</h1>
        <p class="mt-1 text-sm text-stone-500">Update {{ $employee->name }}'s details</p>
    </div>

    <div class="rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('employees.update', $employee) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('employees._form-fields', ['employee' => $employee, 'departments' => $departments])
        </form>
    </div>
@endsection
