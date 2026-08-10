@props([
    'title',
    'subtitle' => null,
    'eyebrow' => 'Gestão do Núcleo de Acessibilidade e Inclusão',
    'meta' => [],
    'status' => null,
])

@php
    $logoPath = public_path('images/gnai.png');
@endphp

<div {{ $attributes->merge(['class' => 'header pdf-header']) }}>
    @if(file_exists($logoPath))
        <img class="pdf-header-corner-logo" src="{{ $logoPath }}" alt="GNAI">
    @endif

    <p class="pdf-header-eyebrow">{{ $eyebrow }}</p>
    <h2>{{ $title }}</h2>

    @if($subtitle)
        <p>{!! \App\Shared\Infrastructure\Security\RichTextSanitizer::sanitize((string) $subtitle) !!}</p>
    @endif
</div>
