@extends('layouts.app')

@section('title', 'Add User')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-stone-900">Add User</h1>
        <p class="mt-1 text-sm text-stone-500">Create a login for a manager or assistant</p>
    </div>

    <div class="max-w-xl rounded-xl border border-stone-200 bg-white p-6">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf
            <x-form-input name="name" label="Full Name" :value="old('name')" required />
            <x-form-input name="email" label="Email" type="email" :value="old('email')" required />
            <x-form-input name="phone" label="Phone" :value="old('phone')" />
            <x-form-select
                name="role"
                label="Role"
                :options="\App\Enums\UserRole::options()"
                :selected="old('role', 'admin')"
                required
            />
            <x-form-input name="password" label="Password" type="password" required />

            <p class="text-xs text-stone-500">
                <strong>Admin</strong> can only access Attendance and Cash Advances.
                <strong>Super Admin</strong> has full access to everything.
            </p>

            <div class="flex gap-3">
                <x-button type="submit">Save User</x-button>
                <x-button href="{{ route('users.index') }}" variant="secondary">Cancel</x-button>
            </div>
        </form>
    </div>
@endsection