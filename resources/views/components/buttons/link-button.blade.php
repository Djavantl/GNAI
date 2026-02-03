@props([
    'href',
    'variant' => 'primary',
])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => "btn btn-sm btn-action {$variant}"
    ]) }}
>
    {{ $slot }}
</a>