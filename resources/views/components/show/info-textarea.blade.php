@props([
    'label',
    'value' => null,
    'column' => 'col-md-12',
    'rich' => true
])

<div {{ $attributes->merge(['class' => $column . ' mb-4 px-4']) }}>
    <label class="d-block fw-bold text-title small mb-2 text-uppercase">
        {{ $label }}
    </label>

    <div class="custom-display-box-textarea">
        @php
            $content = $slot->isNotEmpty() ? $slot : ($value ?? '---');
        @endphp

        <div class="textarea-content-wrapper">
            @if($rich)
                {!! $content !!}
            @else
                {!! nl2br(e($content)) !!}
            @endif
        </div>
    </div>
</div>
