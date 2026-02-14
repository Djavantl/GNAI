@props([
    'href',
    'variant' => 'primary',
])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => "btn btn-sm btn-action {$variant} align-items-center"
    ]) }}
>
    {{ $slot }}
</a>
