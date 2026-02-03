@props([
    'variant' => 'primary',
])

<button
    type="submit"
    {{ $attributes->merge([
        'class' => "btn btn-sm btn-action {$variant}"
    ]) }}
>
    {{ $slot }}
</button>
