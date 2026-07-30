@extends('layouts.app')

@section('title', __('app.advance_types.title'))

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.advance_types.title') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.advance_types.subtitle') }}</p>
        </div>
        <x-button href="{{ route('advance-types.create') }}">{{ __('app.advance_types.add_advance_type') }}</x-button>
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.advance_type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.advance_types.cash_advances_recorded') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($advanceTypes as $advanceType)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ $advanceType->display_name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $advanceType->cash_advances_count }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('advance-types.edit', $advanceType) }}" class="font-medium text-emerald-700 hover:text-emerald-800">{{ __('app.common.edit') }}</a>
                            @if ($advanceType->cash_advances_count === 0)
                                <form method="POST" action="{{ route('advance-types.destroy', $advanceType) }}" class="ml-4 inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-700" onclick="return confirm('{{ __('app.advance_types.confirm_delete') }}')">
                                        {{ __('app.common.delete') }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.advance_types.no_advance_types_yet') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection