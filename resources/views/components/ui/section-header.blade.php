{{-- resources/views/components/ui/section-header.blade.php --}}

@props([
    'target',
    'title',
    'description' => null,
    'startOpen' => false
])

@php
    $expanded = $startOpen ? 'true' : 'false';
@endphp

<div class="ctx-section">
    {{-- Mantivemos a classe original do header --}}
    <div class="ctx-section-header d-flex align-items-center justify-content-between">
        
        <div class="d-flex align-items-center gap-2">
            {{-- 1. O Toggle original movido para o INÍCIO --}}
            <button
                type="button"
                class="ctx-toggle" {{-- Classe original preservada --}}
                data-target="{{ $target }}"
                aria-expanded="{{ $expanded }}"
                aria-controls="{{ $target }}"
            >
                <i class="ctx-chevron fas fa-chevron-down"></i>
            </button>

            {{-- 2. Título e Descrição --}}
            <div class="ctx-section-title">
                <h5 class="mb-0">{{ $title }}</h5>
                @if($description)
                    <div class="small text-muted">
                        {{ $description }}
                    </div>
                @endif
            </div>
        </div>

        {{-- 3. Novo slot para o botão "Ver" ou outras ações no FINAL --}}
        @if(isset($actions))
            <div class="ctx-section-actions">
                {{ $actions }}
            </div>
        @endif

    </div>
</div>