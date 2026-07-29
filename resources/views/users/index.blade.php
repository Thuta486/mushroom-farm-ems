@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Users</h1>
            <p class="mt-1 text-sm text-stone-500">Manage who can log in and what they can access</p>
        </div>
        <x-button href="{{ route('users.create') }}">Add User</x-button>
    </div>

    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-[900px] w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Role</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">
                            <x-status-badge :status="$user->role->value" />
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('users.edit', $user) }}" class="font-medium text-emerald-700 hover:text-emerald-800">Edit</a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}" class="ml-4 inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-700" onclick="return confirm('Delete this user?')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-stone-500">
                            No users yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection