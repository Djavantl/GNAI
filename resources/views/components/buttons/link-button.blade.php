@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'label' => null,
    'type' => 'button'
])

@php
    $sizeClass = $size !== 'md' ? "btn-{$size}" : '';
    $classes = "btn-action {$variant} {$sizeClass}";
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    {{ $href ? "href=$href role=button" : "type=$type" }}
    {{ $attributes->merge([
        'class' => $classes,
        'aria-label' => $label
    ]) }}
>
    {{ $slot }}
</{{ $tag }}>
