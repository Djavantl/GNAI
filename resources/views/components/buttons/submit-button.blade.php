@props([
    'variant' => 'primary',
    'size' => 'md',
    'label' => null,
    'disabled' => false,
])

@php
    $sizeClass = $size !== 'md' ? "btn-{$size}" : '';
    $classes = "btn-action {$variant} {$sizeClass} d-inline-flex align-items-center justify-content-center";
@endphp

<button
    type="submit"
    {{ $attributes->merge([
        'class' => $classes,
        'aria-label' => $label,
        'disabled' => $disabled,
    ]) }}
>
    {{ $slot }}
</button>
