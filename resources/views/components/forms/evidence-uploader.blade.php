@props([
    'name' => 'evidences[]',
    'label' => 'Evidências',
    'existingImages' => [],
    'ariaLabel' => 'Escolher arquivos de evidência para upload'
])

@php
    $cleanId = str_replace(['[', ']'], '', $name);
@endphp

<div class="mb-3 evidence-uploader">
    {{-- Label conectado ao input --}}
    <label for="input-{{ $cleanId }}" class="form-label fw-bold text-purple-dark">
        {{ $label }}
    </label>

    {{-- Input oculto --}}
    <input type="file"
           id="input-{{ $cleanId }}"
           name="{{ $name }}"
           multiple
           accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.ppt,.pptx,.odp,.odt,.ods,.xls,.xlsx,.csv,.txt"
           class="d-none"
           aria-describedby="help-{{ $cleanId }}">

    {{-- Container de previews --}}
    <div class="preview-container d-flex flex-wrap gap-2" role="list" aria-live="polite">
        @foreach($existingImages as $img)
            @php
                $extension = strtolower(pathinfo((string) $img, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);
            @endphp
            <div class="position-relative d-inline-block" role="listitem" style="width:70px;height:70px;">
                <a href="{{ asset('storage/' . $img) }}" target="_blank" class="d-flex align-items-center justify-content-center rounded border bg-light text-secondary text-decoration-none w-100 h-100">
                    @if($isImage)
                        <img src="{{ asset('storage/' . $img) }}"
                             alt="Miniatura da evidência"
                             class="rounded border"
                             style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <i class="fas fa-file-alt fa-lg" aria-hidden="true"></i>
                    @endif
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
        Você pode selecionar múltiplas evidências: imagens, PDF, documentos, planilhas, textos e apresentações.
    </div>
</div>

{{-- Script JS --}}
@vite('resources/js/pages/inclusive-radar/evidence-uploader.js')
