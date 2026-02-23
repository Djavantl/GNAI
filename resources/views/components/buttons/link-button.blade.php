@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'label' => null,
    'type' => 'button'
])

@php
    $sizeClass = $size !== 'md' ? $size : '';
    $classes = "btn-action {$variant} {$sizeClass}";
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if($href)
    href="{{ $href }}"
@else
    type="{{ $type }}"
@endif

{{ $attributes->merge([
    'class' => $classes,
    'aria-label' => $label
]) }}
>
{{ $slot }}
</{{ $tag }}>
