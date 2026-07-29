@props(['label', 'for'])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'mb-1 block text-sm font-medium text-stone-700']) }}>
    {{ $label }}
</label>
