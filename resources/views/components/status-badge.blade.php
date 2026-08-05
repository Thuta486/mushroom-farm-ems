@props(['status'])

@php
    $classes = match ($status) {
        'active' => 'bg-emerald-100 text-emerald-800',
        'inactive' => 'bg-amber-100 text-amber-800',
        'terminated' => 'bg-red-100 text-red-800',
        'present' => 'bg-emerald-100 text-emerald-800',
        'absent' => 'bg-red-100 text-red-800',
        'paid' => 'bg-emerald-100 text-emerald-800',
        'unpaid' => 'bg-amber-100 text-amber-800',
        'superadmin' => 'bg-emerald-100 text-emerald-800',
        'admin' => 'bg-stone-100 text-stone-700',
        default => 'bg-stone-100 text-stone-700',
    };

    $label = match ($status) {
        'active' => __('app.status.active'),
        'inactive' => __('app.status.inactive'),
        'terminated' => __('app.status.terminated'),
        'present' => __('app.status.present'),
        'absent' => __('app.status.absent'),
        'paid' => __('app.status.paid'),
        'unpaid' => __('app.status.unpaid'),
        'superadmin' => __('app.status.superadmin'),
        'admin' => __('app.status.admin'),
        default => ucfirst((string) $status),
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {$classes}"]) }}>
    {{ $label }}
</span>
