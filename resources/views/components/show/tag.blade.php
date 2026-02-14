@props([
    'color' => 'light',
])

@php
    $bgClass = $color === 'dark' ? 'bg-purple-dark text-white' : 'bg-purple-light';
@endphp

<span {{ $attributes->merge(['class' => "tag-item {$bgClass}"]) }}>
    {{ $slot }}
</span>
