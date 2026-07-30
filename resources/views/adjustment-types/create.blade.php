@extends('layouts.app')

@section('title', __('app.adjustment_types.add_adjustment_type'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.adjustment_types.add_adjustment_type') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.adjustment_types.create_new_category') }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('adjustment-types.store') }}" class="space-y-5">
            @csrf
            <x-form-input name="name_en" label="{{ __('app.common.adjustment_type_en') }}" :value="old('name_en')" required />
            <x-form-input name="name_my" label="{{ __('app.common.adjustment_type_my') }}" :value="old('name_my')" required />

            <x-form-select name="category" label="{{ __('app.common.category') }}" :options="['bonus' => __('app.adjustment_types.category_bonus'), 'deduction' => __('app.adjustment_types.category_deduction')]" :selected="old('category', 'deduction')" required />

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.adjustment_types.save_adjustment_type') }}</x-button>
                <x-button href="{{ route('adjustment-types.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection
