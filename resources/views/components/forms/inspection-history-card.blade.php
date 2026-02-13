@props(['inspection'])

@php
    $isBarrier = str_contains($inspection->inspectable_type, 'barrier');
@endphp

<div {{ $attributes->merge(['class' => 'card mb-3 shadow-sm border-0 overflow-hidden']) }}>
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 border-bottom-0">
        <span class="badge bg-purple-dark px-3" style="background-color: #4D44B5; color: white;">
            {{ $inspection->inspection_date->format('d/m/Y') }}
        </span>
        <span class="text-uppercase fw-bold small text-muted" style="letter-spacing: 1px;">
            {{ $inspection->type->label() }}
        </span>
    </div>

    <div class="card-body pt-0 pb-3">
        <div class="row g-0">

            {{-- Lado Esquerdo: Texto --}}
            <div class="col-md-7 border-end pe-4">
                <div class="pt-3">
                    @if($isBarrier)
                        {{-- SE FOR BARREIRA: Sempre mostra Status da Barreira (mesmo se for 'initial') --}}
                        <label class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                            Status da Barreira
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            {{-- Aqui usamos o campo 'status' do Model Inspection (que é o BarrierStatus Enum) --}}
                            <span class="fw-bold text-purple-dark fs-5 {{ $inspection->status?->color()}}">
                                {{ $inspection->status?->label() ?? 'Identificada' }}
                            </span>
                        </div>
                    @else
                        {{-- SE NÃO FOR BARREIRA (TA, MPA, etc): Mostra Estado de Conservação --}}
                        <label class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                            Estado de Conservação
                        </label>
                        <div class="d-flex align-items-center gap-2">
                            {{-- Aqui usamos o campo 'state' do Model Inspection (que é o ConservationState Enum) --}}
                            <span class="fw-bold text-purple-dark fs-5">
                                {{ $inspection->state?->label() ?? '---' }}
                            </span>
                        </div>
                    @endif
                </div>

                @if($inspection->description)
                    <div class="mt-3">
                        <label class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                            Parecer Técnico
                        </label>
                        <p class="history-description-text mb-0" style="font-size: 0.85rem; color: #666;">
                            {{ $inspection->description }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Lado Direito: Imagens --}}
            <div class="col-md-5 ps-md-4">
                <div class="pt-3">
                    <label class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                        Evidências Visuais
                    </label>

                    @if($inspection->images && $inspection->images->count() > 0)
                        <div class="d-flex flex-wrap gap-2 pt-1">
                            @foreach($inspection->images as $img)
                                <div class="position-relative d-inline-block" style="width:70px; height:70px;">
                                    <a href="{{ asset('storage/' . $img->path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $img->path) }}"
                                             class="rounded border shadow-sm"
                                             style="width:100%; height:100%; object-fit:cover;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3 bg-light rounded border border-dashed mt-1">
                            <span class="text-muted small" style="font-size:0.7rem;">
                                Nenhuma foto registrada
                            </span>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
