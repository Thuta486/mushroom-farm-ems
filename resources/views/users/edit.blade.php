@extends('layouts.app')

@section('title', __('app.users.edit_user'))

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.users.edit_user') }}</h1>
        <p class="mt-1 text-sm text-stone-500">{{ __('app.users.edit_user_subtitle', ['name' => $user->display_name]) }}</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input name="name" label="{{ __('app.common.full_name') }}" :value="old('name', $user->name)" required />
            <x-form-input name="email" label="{{ __('app.common.email') }}" type="email" :value="old('email', $user->email)" required />
            <x-form-input name="phone" label="{{ __('app.common.phone') }}" :value="old('phone', $user->phone)" />
            <x-form-select
                name="role"
                label="{{ __('app.common.role') }}"
                :options="\App\Enums\UserRole::options()"
                :selected="old('role', $user->role->value)"
                required
            />
            <x-form-input name="password" label="{{ __('app.users.new_password') }}" type="password" />

            <p class="text-xs text-stone-500">
                <strong>{{ __('app.users.admin') }}</strong> {{ __('app.users.admin_description') }}
                <strong>{{ __('app.users.super_admin') }}</strong> {{ __('app.users.super_admin_description') }}
            </p>

            <div class="flex gap-3">
                <x-button type="submit">{{ __('app.users.update_user') }}</x-button>
                <x-button href="{{ route('users.index') }}" variant="secondary">{{ __('app.common.cancel') }}</x-button>
            </div>
        </form>
    </div>
@endsection