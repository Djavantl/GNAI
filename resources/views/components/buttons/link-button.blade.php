@props([
    'href',
    'variant' => 'primary',
    'size' => 'md',
    'label' => null
])

@php
    $sizeClass = $size !== 'md' ? $size : '';
    $classes = "btn-action {$variant} {$sizeClass}";
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => $classes,
        'aria-label' => $label
    ]) }}
>
    {{ $slot }}
</a>
