@props([
    'label',
    'value' => null,
    'column' => 'col-md-12',
    'rich' => true,
    'maxLines' => 6,
    'scrollable' => true
])

<div {{ $attributes->merge(['class' => $column . ' mb-4 px-4']) }}>
    <label class="d-block fw-bold text-title small mb-2 text-uppercase">
        {{ $label }}
    </label>

    <div class="custom-display-box-textarea">
        @php
            $content = $slot->isNotEmpty()
                ? $slot
                : ($value ?? '---');

            if ($rich) {
                $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $content = \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $content);
            }

            $maxHeight = $maxLines * 1.5; // altura baseada em line-height
        @endphp

        <div
            class="textarea-content-wrapper"
            style="
                max-height: {{ $scrollable ? $maxHeight . 'em' : 'none' }};
                overflow-y: {{ $scrollable ? 'auto' : 'visible' }};
                padding-right: 4px;
            "
        >
            @if($rich)
                {!! $content !!}
            @else
                {{ $content }}
            @endif
        </div>
    </div>
</div>
