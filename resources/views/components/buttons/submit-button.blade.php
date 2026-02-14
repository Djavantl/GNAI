@props([
    'variant' => 'primary',
])

<button
    type="submit"
    {{ $attributes->merge([
        'class' => "btn btn-sm btn-action {$variant} align-items-center"
    ]) }}
>
    {{ $slot }}
</button>
