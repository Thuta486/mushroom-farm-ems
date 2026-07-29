@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Edit Department</h1>
        <p class="mt-1 text-sm text-stone-500">Update department name</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('departments.update', $department) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input name="name" label="Department Name" :value="old('name', $department->name)" required />

            <div class="flex gap-3">
                <x-button type="submit">Save Changes</x-button>
                <x-button href="{{ route('departments.index') }}" variant="secondary">Cancel</x-button>
            </div>
        </form>
    </div>
@endsection
