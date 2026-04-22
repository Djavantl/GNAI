@props([
    'variant' => 'primary',
    'size' => 'md',
    'label' => null,
    'disabled' => false,
    'type' => 'submit',
])

@php
    $sizeClass = $size !== 'md' ? "btn-{$size}" : '';
    $classes = "btn-action {$variant} {$sizeClass} d-inline-flex align-items-center justify-content-center";
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => $classes,
        'aria-label' => $label,
        'disabled' => $disabled,
    ]) }}
>
    {{ $slot }}
</button>
