@props([
    'label' => null,
    'value' => null,
    'rows' => 6,        
    'column' => 'col-md-12',
    'scroll' => true,
    'empty' => '—'
])

<div class="{{ $column }}">
    <div {{ $attributes->merge([
        'class' => 'card p-3 border-light bg-soft-info h-100'
    ]) }}>

        @if($label)
            <span class="small text-muted d-block mb-1">
                {{ $label }}
            </span>
        @endif

        @php
            // calcula altura baseada em rows
            $lineHeight = 1.5; // em rem
            $height = $rows * $lineHeight;
        @endphp

        <div
            style="
                min-height: {{ $height }}rem;
                max-height: {{ $height }}rem;
                overflow-y: {{ $scroll ? 'auto' : 'visible' }};
                white-space: pre-line;
            "
            class="small"
        >
            @if(trim($slot) !== '')
                {{ $slot }}
            @else
                {{ $value ?? $empty }}
            @endif
        </div>

    </div>
</div>