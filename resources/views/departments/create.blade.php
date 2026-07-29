@extends('layouts.app')

@section('title', __('app.departments.title_add'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.departments.title_add') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.departments.subtitle_add') }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('departments.store') }}" class="space-y-5">
            @csrf
            <x-form-input name="name" label="{{ __('app.departments.name') }}" :value="old('name')" required />

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.departments.save_department') }}</x-button>
                <x-button href="{{ route('departments.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection
