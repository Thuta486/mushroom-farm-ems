@extends('layouts.app')

@section('title', __('app.advance_types.edit_advance_type'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.advance_types.edit_advance_type') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.advance_types.update_name') }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('advance-types.update', $advanceType) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input name="name_en" label="{{ __('app.common.advance_type_en') }}" :value="old('name_en', $advanceType->name_en)" required />
            <x-form-input name="name_my" label="{{ __('app.common.advance_type_my') }}" :value="old('name_my', $advanceType->name_my)" required />

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.advance_types.update_advance_type') }}</x-button>
                <x-button href="{{ route('advance-types.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection