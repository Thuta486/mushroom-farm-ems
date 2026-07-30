@extends('layouts.app')

@section('title', __('app.adjustment_types.title'))

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-stone-900">{{ __('app.adjustment_types.title') }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ __('app.adjustment_types.subtitle') }}</p>
        </div>
        <x-button href="{{ route('adjustment-types.create') }}">{{ __('app.adjustment_types.add_adjustment_type') }}</x-button>
    </div>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full divide-y divide-stone-200">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.adjustment_type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.adjustment_types.adjustments_recorded') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-stone-500">{{ __('app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse ($adjustmentTypes as $adjustmentType)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-stone-900">{{ $adjustmentType->display_name }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $adjustmentType->payroll_adjustments_count }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('adjustment-types.edit', $adjustmentType) }}" class="font-medium text-emerald-700 hover:text-emerald-800">{{ __('app.common.edit') }}</a>
                            @if ($adjustmentType->payroll_adjustments_count === 0)
                                <form method="POST" action="{{ route('adjustment-types.destroy', $adjustmentType) }}" class="ml-4 inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:text-red-700" onclick="return confirm('{{ __('app.adjustment_types.confirm_delete') }}')">
                                        {{ __('app.common.delete') }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-stone-500">
                            {{ __('app.adjustment_types.no_adjustment_types_yet') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
