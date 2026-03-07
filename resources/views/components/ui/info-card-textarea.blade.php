@props([
    'label' => null,
    'value' => null,
    'rows' => 20,        
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
            $lineHeight = 1.5;
            $height = $rows * $lineHeight;
        @endphp

        <div
            class="small custom-display-box-textarea"
            style="overflow-y: {{ $scroll ? 'auto' : 'visible' }};"
        >
            @if(trim($slot) !== '')
                {!! $slot !!}
            @else
                {!! $value ?? $empty !!}
            @endif
        </div>

    </div>
</div>