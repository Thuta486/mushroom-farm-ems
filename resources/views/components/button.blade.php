@props(['href' => null, 'variant' => 'primary', 'type' => 'button'])

@php
    $classes = match ($variant) {
        'secondary' => 'border border-stone-300 bg-white text-stone-700 hover:bg-stone-50',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        default => 'bg-emerald-700 text-white hover:bg-emerald-800',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium {$classes}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium {$classes}"]) }}>
        {{ $slot }}
    </button>
@endif
