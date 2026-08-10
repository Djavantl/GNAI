@props(['label', 'value' => null, 'column' => 'col-md-6', 'isBox' => false])

<div {{ $attributes->merge(['class' => $column . ' mb-4 px-4']) }}>
    {{-- Título visual do campo (escondido para leitores de tela para evitar repetição) --}}
    <span class="d-block fw-bold text-title small mb-1 text-uppercase" aria-hidden="true">
        {{ $label }}
    </span>

    @php
        $hasSlot = $slot->isNotEmpty();
        $displayValue = $hasSlot ? $slot->toHtml() : ($value ?? '---');
        $displayValue = (string) $displayValue;

        $decodedValue = html_entity_decode($displayValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainTextValue = trim(preg_replace('/\s+/', ' ', strip_tags($decodedValue)));
        $plainTextValue = $plainTextValue !== '' ? $plainTextValue : '---';

        $hasEncodedHtmlTags = preg_match('/&lt;\/?[a-z]/i', $displayValue) === 1;
        $shouldRenderPlainText = !$hasSlot || $hasEncodedHtmlTags;
    @endphp

    <div class="{{ $isBox ? 'custom-display-box' : 'text-base' }}"
         style="{{ !$isBox ? 'color: var(--text-purple-dark); font-size: 1.05rem;' : '' }}"
         role="text"
         aria-label="{{ $label }}: {{ $plainTextValue }}">
        @if($shouldRenderPlainText)
            {{ $plainTextValue }}
        @else
            {{ $slot }}
        @endif
    </div>
</div>
