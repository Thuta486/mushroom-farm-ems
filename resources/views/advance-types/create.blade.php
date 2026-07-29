@extends('layouts.app')

@section('title', 'Add Advance Type')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Add Advance Type</h1>
        <p class="mt-1 text-sm text-stone-500">Create a new category for cash advances</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('advance-types.store') }}" class="space-y-5">
            @csrf
            <x-form-input name="name" label="Advance Type Name" :value="old('name')" required />

            <div class="flex gap-3">
                <x-button type="submit">Save Advance Type</x-button>
                <x-button href="{{ route('advance-types.index') }}" variant="secondary">Cancel</x-button>
            </div>
        </form>
    </div>
@endsection