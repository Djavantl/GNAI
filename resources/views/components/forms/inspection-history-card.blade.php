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
            <div class="col-md-7 border-end pe-4">
                <div class="pt-3">
                    @if($isBarrier)
                        <span class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                            Status da Barreira
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-purple-dark fs-5 {{ $inspection->status?->color()}}">
                                {{ $inspection->status?->label() ?? 'Identificada' }}
                            </span>
                        </div>
                    @else
                        <span class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                            Estado de Conservação
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-purple-dark fs-5">
                                {{ $inspection->state?->label() ?? '---' }}
                            </span>
                        </div>
                    @endif
                </div>

                @if($inspection->description)
                    <div class="mt-3">
                        <span class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1; text-transform: uppercase;">
                            Parecer Técnico / Descrição
                        </span>

                        <div class="history-description-text">
                            {!! \App\Support\RichTextSanitizer::sanitize((string) $inspection->description) !!}
                        </div>
                    </div>
                @endif
            </div>

            @php
                $isBarrier = str_contains($inspection->inspectable_type, 'barrier');
            @endphp

            <div class="col-md-5 ps-md-4">
                <div class="pt-3">
                    <span class="d-block text-muted uppercase fw-bold mb-2" style="font-size: 0.65rem; line-height: 1;">
                        Evidências
                    </span>

                    @if($inspection->evidences && $inspection->evidences->count() > 0)

                        @if($isBarrier)
                            <div class="row g-2 pt-1">
                                @foreach($inspection->evidences as $img)
                                    <div class="col-4">
                                        <div class="position-relative" style="aspect-ratio: 1/1;">
                                            <x-forms.evidence-card :evidence="$img" compact />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="d-flex flex-wrap gap-2 pt-1">
                                @foreach($inspection->evidences as $img)
                                    <div class="position-relative d-inline-block" style="width:70px; height:70px;">
                                        <x-forms.evidence-card :evidence="$img" compact />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3 bg-light rounded border border-dashed mt-1">
                            <span class="text-muted small" style="font-size:0.7rem;">
                                Nenhuma evidência registrada
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
