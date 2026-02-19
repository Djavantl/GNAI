@props([
    'variant' => 'primary',
    'size' => 'md',
    'label' => null
])

@php
    $sizeClass = $size !== 'md' ? $size : '';
    $classes = "btn-action {$variant} {$sizeClass} align-items-center";
@endphp

<button
    type="submit"
    {{ $attributes->merge([
        'class' => $classes,
        'aria-label' => $label
    ]) }}
>
    {{ $slot }}
</button>
