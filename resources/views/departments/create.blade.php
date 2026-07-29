@extends('layouts.app')

@section('title', 'Add Department')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Add Department</h1>
        <p class="mt-1 text-sm text-stone-500">Create a new department for grouping employees</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('departments.store') }}" class="space-y-5">
            @csrf
            <x-form-input name="name" label="Department Name" :value="old('name')" required />

            <div class="flex gap-3">
                <x-button type="submit">Save Department</x-button>
                <x-button href="{{ route('departments.index') }}" variant="secondary">Cancel</x-button>
            </div>
        </form>
    </div>
@endsection
