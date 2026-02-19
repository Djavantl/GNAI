@props([
    'name' => 'images[]',
    'label' => 'Fotos de Evidência',
    'existingImages' => [],
    'ariaLabel' => 'Escolher arquivos de imagens para upload'
])

@php
    $cleanId = str_replace(['[', ']'], '', $name);
@endphp

<div class="mb-3 image-uploader">
    {{-- Label conectado ao input --}}
    <label for="input-{{ $cleanId }}" class="form-label fw-bold text-purple-dark">
        {{ $label }}
    </label>

    {{-- Input oculto --}}
    <input type="file"
           id="input-{{ $cleanId }}"
           name="{{ $name }}"
           multiple
           accept="image/*"
           class="d-none"
           aria-describedby="help-{{ $cleanId }}">

    {{-- Container de previews --}}
    <div class="preview-container d-flex flex-wrap gap-2" role="list" aria-live="polite">
        @foreach($existingImages as $img)
            <div class="position-relative d-inline-block" role="listitem" style="width:70px;height:70px;">
                {{-- Removido title do link da imagem --}}
                <a href="{{ asset('storage/' . $img) }}" target="_blank" class="d-block">
                    <img src="{{ asset('storage/' . $img) }}"
                         alt="Miniatura da imagem de evidência"
                         class="rounded border"
                         style="width:100%;height:100%;object-fit:cover;">
                </a>
            </div>
        @endforeach
    </div>

    {{-- Botão de ação (Removido :title redundante) --}}
    <x-buttons.link-button
        href="javascript:void(0)"
        class="mt-2 mb-3"
        onclick="document.getElementById('input-{{ $cleanId }}').click()"
        variant="primary"
        :label="$ariaLabel"
    >
        <i class="fas fa-upload me-1"></i> Escolher Arquivos
    </x-buttons.link-button>

    {{-- Texto explicativo --}}
    <div id="help-{{ $cleanId }}" class="d-block text-muted" style="font-size: 0.75rem;">
        Você pode selecionar múltiplos arquivos de imagem.
    </div>
</div>

{{-- Script JS --}}
@vite('resources/js/pages/inclusive-radar/image-uploader.js')
