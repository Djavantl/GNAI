@props([
    'name' => 'images[]',
    'label' => 'Fotos de Evidência',
    'existingImages' => [],
])

<div class="mb-3 image-uploader">
    {{-- Label padronizado --}}
    <label class="form-label fw-bold text-purple-dark">{{ $label }}</label>

    {{-- Input oculto --}}
    <input type="file"
           id="input-{{ $name }}"
           name="{{ $name }}"
           multiple
           accept="image/*"
           class="d-none"
           aria-describedby="help-{{ $name }}">

    {{-- Container de previews --}}
    <div class="preview-container d-flex flex-wrap gap-2" role="list" aria-live="polite">
        @foreach($existingImages as $img)
            <div class="position-relative d-inline-block" role="listitem" style="width:70px;height:70px;">
                <a href="{{ asset('storage/' . $img) }}" target="_blank" class="d-block">
                    <img src="{{ asset('storage/' . $img) }}"
                         alt="Imagem existente"
                         class="rounded border"
                         style="width:100%;height:100%;object-fit:cover;">
                </a>
            </div>
        @endforeach
    </div>

    {{-- Botão padronizado usando o link-button --}}
    <x-buttons.link-button
        href="javascript:void(0)"
        class="mt-2 mb-3"
        onclick="document.getElementById('input-{{ $name }}').click()"
        variant="primary"
        aria-label="Escolher arquivos de imagens para upload"
    >
        Escolher Arquivos
    </x-buttons.link-button>

    {{-- Texto explicativo padronizado --}}
    <div id="help-{{ $name }}" class="d-block text-muted" style="font-size: 0.75rem;">
        Você pode selecionar múltiplos arquivos de imagem. Use Tab e Enter para navegar e remover arquivos.
    </div>
</div>

{{-- Script JS --}}
@vite('resources/js/pages/inclusive-radar/image-uploader.js')
