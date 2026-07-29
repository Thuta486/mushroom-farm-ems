@extends('layouts.app')

@section('title', __('app.users.add_user'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.users.add_user') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.users.create_login') }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf
            <x-form-input name="name" label="{{ __('app.common.full_name') }}" :value="old('name')" required />
            <x-form-input name="email" label="{{ __('app.common.email') }}" type="email" :value="old('email')" required />
            <x-form-input name="phone" label="{{ __('app.common.phone') }}" :value="old('phone')" />
            <x-form-select
                name="role"
                label="{{ __('app.common.role') }}"
                :options="\App\Enums\UserRole::options()"
                :selected="old('role', 'admin')"
                required
            />
            <x-form-input name="password" label="{{ __('app.common.password') }}" type="password" required />

            <p class="text-xs text-stone-500">
                <strong>{{ __('app.users.admin') }}</strong> {{ __('app.users.admin_description') }}
                <strong>{{ __('app.users.super_admin') }}</strong> {{ __('app.users.super_admin_description') }}
            </p>

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.users.save_user') }}</x-button>
                <x-button href="{{ route('users.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection