@extends('layouts.app')

@section('title', 'Departments')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">Departments</h1>
            <p class="mt-1 text-sm text-stone-500">Organize employees by farm area or role group</p>
        </div>
        <x-button href="{{ route('departments.create') }}">Add Department</x-button>
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">Employees</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($departments as $department)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ $department->name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $department->employees_count }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('departments.edit', $department) }}" class="font-medium text-emerald-700 hover:text-emerald-800">Edit</a>
                            @if ($department->employees_count === 0)
                                <form method="POST" action="{{ route('departments.destroy', $department) }}" class="ml-4 inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-700" onclick="return confirm('Delete this department?')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-stone-500">
                            No departments yet. Add departments like Harvesting, Packaging, or Cleaning.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
