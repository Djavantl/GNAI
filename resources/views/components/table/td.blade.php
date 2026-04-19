@props([
    'class' => 'align-middle text-base text-nowrap text-purple-light',
    'responsive' => true
])

<td {{ $attributes->merge(['class' => $class . ($responsive ? ' d-none d-md-table-cell' : '')]) }}
    style="color: var(--text-purple-light); padding: 1rem;">
    {{ $slot }}
</td>
