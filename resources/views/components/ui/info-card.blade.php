@props([
    'label' => null,
    'value' => null,
    'column' => 'col-md-4',
    'class' => '',
    'bg' => 'bg-soft-info',
])

<div class="{{ $column }}">
    <div {{ $attributes->merge([
        'class' => "card p-3 border-light $bg h-100 $class"
    ]) }}>

        @if($label)
            <span class="small text-muted d-block">
                {{ $label }}
            </span>
        @endif

        @if($value !== null)
            <strong class="d-block">
                {{ $value }}
            </strong>
        @endif

        {{-- conteúdo customizado --}}
        @if(trim($slot))
            <div class="{{ $value !== null ? 'mt-1' : '' }}">
                {{ $slot }}
            </div>
        @endif

    </div>
</div>