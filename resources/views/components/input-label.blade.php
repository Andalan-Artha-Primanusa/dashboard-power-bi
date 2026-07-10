@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-[#BD9B75]']) }}>
 {{ $value ?? $slot }}
</label>
