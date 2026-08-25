@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-xs uppercase tracking-wider text-gray-600']) }}>
    {{ $value ?? $slot }}
</label>
