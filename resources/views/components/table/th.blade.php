@props([
    'class' => 'align-middle fw-bold text-title'
])
<th {{ $attributes->merge(['class' => $class]) }}; padding: 1.2rem 1rem;">
    {{ $slot }}
</th>
