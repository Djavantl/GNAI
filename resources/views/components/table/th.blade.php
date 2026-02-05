@props([
    'class' => 'align-middle fw-bold text-title'
])
<th {{ $attributes->merge(['class' => $class]) }}; style="padding: 1rem;"">
    {{ $slot }}
</th>
