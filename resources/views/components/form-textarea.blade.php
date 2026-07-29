@props(['name', 'label', 'value' => null, 'rows' => 3])

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    <x-form-label :for="$name" :label="$label" />
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        class="block w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
