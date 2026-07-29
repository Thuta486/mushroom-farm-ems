@props(['status'])

@php
    $classes = match ($status) {
        'active' => 'bg-emerald-100 text-emerald-800',
        'inactive' => 'bg-amber-100 text-amber-800',
        'terminated' => 'bg-red-100 text-red-800',
        default => 'bg-stone-100 text-stone-700',
    };

    $label = match ($status) {
        'active' => 'Active',
        'inactive' => 'Inactive',
        'terminated' => 'Terminated',
        default => ucfirst((string) $status),
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {$classes}"]) }}>
    {{ $label }}
</span>
