@props([
    'variant' => 'primary',
    'size' => 'md',
    'label' => null,
    'disabled' => false,
])

@php
    $sizeClass = $size !== 'md' ? $size : '';
    $classes = "btn-action {$variant} {$sizeClass} align-items-center";
@endphp

<button
    type="submit"
    {{ $attributes->merge([
        'class' => $classes,
        'aria-label' => $label,
    ]) }}
    @if($disabled) disabled @endif
>
    {{ $slot }}
</button>
