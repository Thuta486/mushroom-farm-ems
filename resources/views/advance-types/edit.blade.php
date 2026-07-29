@extends('layouts.app')

@section('title', 'Edit Advance Type')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Edit Advance Type</h1>
        <p class="mt-1 text-sm text-stone-500">Update this advance type's name</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('advance-types.update', $advanceType) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input name="name" label="Advance Type Name" :value="old('name', $advanceType->name)" required />

            <div class="flex gap-3">
                <x-button type="submit">Update Advance Type</x-button>
                <x-button href="{{ route('advance-types.index') }}" variant="secondary">Cancel</x-button>
            </div>
        </form>
    </div>
@endsection