@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false])

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    <x-form-label :for="$name" :label="$label" />
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        @if($required) required @endif
        class="block w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
    />
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
