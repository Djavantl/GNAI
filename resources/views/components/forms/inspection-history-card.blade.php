@props(['inspection'])

<div {{ $attributes->merge(['class' => 'card mb-3 shadow-sm border-0 overflow-hidden']) }}>
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom-0">
        <span class="badge bg-purple-dark px-3" style="background-color: #4D44B5;">{{ $inspection->inspection_date->format('d/m/Y') }}</span>
        <span class="text-uppercase fw-bold small text-muted" style="letter-spacing: 1px;">
            {{ $inspection->type->name }}
        </span>
    </div>

    <div class="card-body pt-0 pb-3">
        <div class="row g-0"> {{-- g-0 para controle total de espaçamento --}}

            {{-- Lado Esquerdo: Texto --}}
            <div class="col-md-7 border-end pe-4">
                <div class="pt-3"> {{-- Padding superior fixo para alinhar com o lado direito --}}
                    <label class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                        Estado de Conservação
                    </label>
                    <div class="d-flex align-items-center gap-2">
                        {{-- Ícone de status opcional para dar peso visual --}}
                        <span class="fw-bold text-purple-dark fs-5">{{ $inspection->state->label() }}</span>
                    </div>
                </div>

                @if($inspection->description)
                    <div class="mt-3">
                        <label class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                            Parecer Técnico
                        </label>
                        <p class="history-description-text">
                            {{ $inspection->description }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Lado Direito: Imagens --}}
            <div class="col-md-5 ps-md-4">
                <div class="pt-3"> {{-- Padding superior idêntico ao lado esquerdo --}}
                    <label class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                        Evidências Visuais
                    </label>

                    @if($inspection->images->count() > 0)
                        <div class="d-flex flex-wrap gap-2 pt-1"> {{-- pt-1 compensa a altura da linha do texto ao lado --}}
                            @foreach($inspection->images as $img)
                                <a href="{{ asset('storage/' . $img->path) }}" target="_blank" class="inspection-photo-link">
                                    <img src="{{ asset('storage/' . $img->path) }}"
                                         class="rounded border transition-all shadow-sm"
                                         style="width: 58px; height: 58px; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 bg-light rounded border border-dashed mt-1">
                            <span class="text-muted small" style="font-size: 0.7rem;">Nenhuma foto registrada</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
