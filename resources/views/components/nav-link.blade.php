@props([
    'href',
    'active' => false,
    'label',
])

<a href="{{ $href }}"
   @class([
       'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
       'bg-emerald-50 text-emerald-800' => $active,
       'text-stone-600 hover:bg-stone-50 hover:text-stone-900' => ! $active,
   ])
   @if($active) aria-current="page" @endif
>
    <span @class([
        'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg',
        'bg-emerald-100 text-emerald-700' => $active,
        'bg-stone-100 text-stone-500 group-hover:bg-stone-200 group-hover:text-stone-700' => ! $active,
    ])>
        {{ $icon }}
    </span>
    <span>{{ $label }}</span>
</a>
