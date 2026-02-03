@props([
    'class' => 'd-flex align-items-center justify-content-center gap-2 table-actions'
])

<div {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</div>