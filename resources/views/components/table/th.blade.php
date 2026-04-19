@props([
    'class' => 'align-middle fw-bold text-title',
    'responsive' => true
])

<th {{ $attributes->merge(['class' => $class . ($responsive ? ' d-none d-md-table-cell' : '')]) }}
    style="padding: 1rem;">
    {{ $slot }}
</th>
