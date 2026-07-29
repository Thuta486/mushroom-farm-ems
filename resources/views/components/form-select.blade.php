@props(['name', 'label', 'options' => [], 'selected' => null, 'placeholder' => null, 'required' => false])

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    <x-form-label :for="$name" :label="$label" />
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @if($required) required @endif
        class="block w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
