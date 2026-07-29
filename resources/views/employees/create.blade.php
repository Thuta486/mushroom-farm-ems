@extends('layouts.app')

@section('title', 'Add Employee')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Add Employee</h1>
        <p class="mt-1 text-sm text-stone-500">Register a new farm worker</p>
    </div>

    <div class="rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
            @csrf
            @include('employees._form-fields', ['employee' => null, 'departments' => $departments])
        </form>
    </div>
@endsection
