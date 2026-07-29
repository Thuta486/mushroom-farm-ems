@extends('layouts.app')

@section('title', __('app.advance_types.add_advance_type'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.advance_types.add_advance_type') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.advance_types.create_new_category') }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('advance-types.store') }}" class="space-y-5">
            @csrf
            <x-form-input name="name" label="{{ __('app.common.name') }}" :value="old('name')" required />

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.advance_types.save_advance_type') }}</x-button>
                <x-button href="{{ route('advance-types.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection