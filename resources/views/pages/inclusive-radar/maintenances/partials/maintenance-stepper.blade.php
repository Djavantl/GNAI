@php
    $stage1 = $maintenance->stages->firstWhere('step_number', 1);
    $stage2 = $maintenance->stages->firstWhere('step_number', 2);
    $step2Clickable = !is_null($stage1?->completed_at);
    $currentStep = request()->routeIs('*.stage1') ? 1 : (request()->routeIs('*.stage2') ? 2 : null);
    $isStage1Completed = !is_null($stage1?->completed_at);
@endphp

<div class="maintenance-stepper-wrapper mb-5" role="group" aria-label="Progresso da manutenção">
    {{-- Container estilo Pill (Baseado no seu Search Filter) --}}
    <div class="stepper-pill-container d-flex align-items-center justify-content-between position-relative shadow-sm">

        {{-- STEP 1 --}}
        <div class="step-pill-item {{ $currentStep == 1 ? 'is-active' : '' }}">
            <a href="{{ route('inclusive-radar.maintenances.stage1', $maintenance) }}"
               class="step-pill-link {{ $isStage1Completed ? 'is-completed' : '' }}">
                <div class="step-pill-icon">
                    @if($isStage1Completed)
                        <i class="fas fa-check"></i>
                    @else
                        <span>1</span>
                    @endif
                </div>
                <div class="step-pill-text">
                    <span class="step-pill-title">Etapa 1 - Dados Iniciais</span>
                    <span class="step-pill-status">{{ $isStage1Completed ? 'Concluído' : 'Em progresso' }}</span>
                </div>
            </a>
        </div>

        {{-- SETA DE CONEXÃO ESTILIZADA --}}
        <div class="step-pill-connector">
            <div class="stepper-arrow {{ $isStage1Completed ? 'arrow-active' : '' }}">
                <i class="fas fa-chevron-right"></i>
                <i class="fas fa-chevron-right arrow-second"></i>
            </div>
        </div>

        {{-- STEP 2 --}}
        <div class="step-pill-item {{ $currentStep == 2 ? 'is-active' : '' }} {{ !$step2Clickable ? 'is-locked' : '' }}">
            @if($step2Clickable)
                <a href="{{ route('inclusive-radar.maintenances.stage2', $maintenance) }}"
                   class="step-pill-link {{ !is_null($stage2?->completed_at) ? 'is-completed' : '' }}">
                    @else
                        <div class="step-pill-link">
                            @endif
                            <div class="step-pill-icon">
                                @if(!is_null($stage2?->completed_at))
                                    <i class="fas fa-check"></i>
                                @else
                                    <span>2</span>
                                @endif
                            </div>
                            <div class="step-pill-text">
                                <span class="step-pill-title">Etapa 2 - Finalização</span>
                                <span class="step-pill-status">
                        {{ !is_null($stage2?->completed_at) ? 'Concluído' : ($step2Clickable ? 'Pendente' : 'Bloqueado') }}
                    </span>
                            </div>
                        @if($step2Clickable)
                </a>
            @else
        </div>
        @endif
    </div>

</div>
</div>

<style>
    /* Container principal arredondado estilo Pill */
    .stepper-pill-container {
        background: #ffffff;
        border: 1px solid #e0e4f1;
        border-radius: 999px;
        padding: 0.5rem 1.5rem;
        margin: 0 auto;
        max-width: 850px;
        display: flex;
        align-items: center;
    }

    .step-pill-item {
        flex: 1;
        display: flex;
        justify-content: center;
        z-index: 2;
    }

    .step-pill-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 20px;
        border-radius: 999px;
        text-decoration: none !important;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    /* Estado Ativo */
    .step-pill-item.is-active .step-pill-link {
        background: rgba(108, 92, 231, 0.08); /* Roxo clarinho de fundo */
        border-color: rgba(108, 92, 231, 0.2);
    }

    /* Ícones Circulares */
    .step-pill-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        background: #f8f9fa;
        color: #9aa3c0;
        border: 1px solid #e0e4f1;
        transition: all 0.3s ease;
    }

    /* Cores de Sucesso */
    .is-completed .step-pill-icon {
        background: #2ecc71 !important;
        color: white !important;
        border-color: #2ecc71 !important;
    }

    /* Cor Roxa (Filtro) para Etapa Ativa */
    .is-active .step-pill-icon {
        background: #6c5ce7;
        color: white;
        border-color: #6c5ce7;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.15);
    }

    /* Textos */
    .step-pill-text {
        display: flex;
        flex-direction: column;
    }

    .step-pill-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #4b5563;
        line-height: 1.2;
    }

    .is-active .step-pill-title {
        color: #6c5ce7;
    }

    .step-pill-status {
        font-size: 0.7rem;
        color: #9aa3c0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Seta de Conexão Estilizada */
    .step-pill-connector {
        flex: 0 0 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stepper-arrow {
        display: flex;
        align-items: center;
        color: #e0e4f1;
        font-size: 1rem;
        position: relative;
    }

    .stepper-arrow.arrow-active {
        color: #6c5ce7; /* Roxo do sistema */
        filter: drop-shadow(0 0 3px rgba(108, 92, 231, 0.3));
        animation: arrowMoving 2s infinite;
    }

    .arrow-second {
        margin-left: -5px;
        opacity: 0.5;
    }

    /* Animação de pulsação lateral */
    @keyframes arrowMoving {
        0% { transform: translateX(0); opacity: 0.5; }
        50% { transform: translateX(5px); opacity: 1; }
        100% { transform: translateX(0); opacity: 0.5; }
    }

    /* Estado Bloqueado */
    .is-locked {
        opacity: 0.5;
        cursor: not-allowed !important;
    }

    .is-locked .step-pill-link {
        pointer-events: none;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .stepper-pill-container {
            border-radius: 20px;
            flex-direction: column;
            padding: 15px;
            gap: 10px;
            max-width: 100%;
        }
        .step-pill-connector {
            transform: rotate(90deg);
            height: 30px;
        }
        .step-pill-item {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
