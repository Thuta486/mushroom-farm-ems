@extends('layouts.app')

@section('title', __('app.departments.title_edit'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.departments.title_edit') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.departments.subtitle_edit') }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('departments.update', $department) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input name="name" label="{{ __('app.departments.name') }}" :value="old('name', $department->name)" required />

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.common.save_changes') }}</x-button>
                <x-button href="{{ route('departments.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection
