@php
    // Se a barreira não existir (create), usamos null para evitar erros
    $barrier = $barrier ?? null;

    // Mapeamento simplificado das etapas
    $stage1 = $barrier ? $barrier->stages->firstWhere('step_number', 1) : null;
    $stage2 = $barrier ? $barrier->stages->firstWhere('step_number', 2) : null;
    $stage3 = $barrier ? $barrier->stages->firstWhere('step_number', 3) : null;
    $stage4 = $barrier ? $barrier->stages->firstWhere('step_number', 4) : null;

    // Verificação de conclusão
    $step1Completed = !is_null($stage1?->completed_at);
    $step2Completed = !is_null($stage2?->completed_at);
    $step3Completed = !is_null($stage3?->completed_at);
    $step4Completed = !is_null($stage4?->completed_at);

    // Lógica de acessibilidade (clicável)
    $step2Clickable = $barrier && $step1Completed;
    $step3Clickable = $barrier && $step2Completed;
    $step4Clickable = $barrier && $step3Completed;

    // Identificação do passo atual baseado na rota
    $currentStep = match (true) {
        request()->routeIs('*.create'), request()->routeIs('*.stage1.*') => 1,
        request()->routeIs('*.stage2*') => 2,
        request()->routeIs('*.stage3*') => 3,
        request()->routeIs('*.stage4*') => 4,
        default => 1,
    };
@endphp

<div class="barrier-stepper-wrapper mb-5" role="group" aria-label="Progresso da barreira">
    <div class="stepper-pill-container d-flex align-items-center justify-content-between position-relative shadow-sm">

        {{-- STEP 1 --}}
        <div class="step-pill-item {{ $currentStep == 1 ? 'is-active' : '' }}">
            @if($barrier)
                <a href="{{ route('inclusive-radar.barriers.stage1.edit', $barrier) }}" class="step-pill-link {{ $step1Completed ? 'is-completed' : '' }}">
                    @else
                        <div class="step-pill-link">
                            @endif
                            <div class="step-pill-icon">
                                @if($step1Completed) <i class="fas fa-check"></i> @else <span>1</span> @endif
                            </div>
                            <div class="step-pill-text">
                                <span class="step-pill-title">Etapa 1 - Identificação</span>
                                <span class="step-pill-status">{{ $step1Completed ? 'Concluído' : 'Em progresso' }}</span>
                            </div>
                        @if($barrier) </a> @else </div> @endif
    </div>

    {{-- Seta 1->2 --}}
    <div class="step-pill-connector">
        <div class="stepper-arrow {{ $step1Completed ? 'arrow-active' : '' }}">
            <i class="fas fa-chevron-right"></i>
            <i class="fas fa-chevron-right arrow-second"></i>
        </div>
    </div>

    {{-- STEP 2 --}}
    <div class="step-pill-item {{ $currentStep == 2 ? 'is-active' : '' }} {{ !$step2Clickable ? 'is-locked' : '' }}">
        @if($step2Clickable)
            <a href="{{ route('inclusive-radar.barriers.stage2', $barrier) }}" class="step-pill-link {{ $step2Completed ? 'is-completed' : '' }}">
                @else
                    <div class="step-pill-link">
                        @endif
                        <div class="step-pill-icon">
                            @if($step2Completed) <i class="fas fa-check"></i> @else <span>2</span> @endif
                        </div>
                        <div class="step-pill-text">
                            <span class="step-pill-title">Etapa 2 - Análise</span>
                            <span class="step-pill-status">{{ $step2Completed ? 'Concluído' : ($step2Clickable ? 'Pendente' : 'Bloqueado') }}</span>
                        </div>
                    @if($step2Clickable) </a> @else </div> @endif
</div>

{{-- Seta 2->3 --}}
<div class="step-pill-connector">
    <div class="stepper-arrow {{ $step2Completed ? 'arrow-active' : '' }}">
        <i class="fas fa-chevron-right"></i>
        <i class="fas fa-chevron-right arrow-second"></i>
    </div>
</div>

{{-- STEP 3 --}}
<div class="step-pill-item {{ $currentStep == 3 ? 'is-active' : '' }} {{ !$step3Clickable ? 'is-locked' : '' }}">
    @if($step3Clickable)
        <a href="{{ route('inclusive-radar.barriers.stage3', $barrier) }}" class="step-pill-link {{ $step3Completed ? 'is-completed' : '' }}">
            @else
                <div class="step-pill-link">
                    @endif
                    <div class="step-pill-icon">
                        @if($step3Completed) <i class="fas fa-check"></i> @else <span>3</span> @endif
                    </div>
                    <div class="step-pill-text">
                        <span class="step-pill-title">Etapa 3 - Tratamento</span>
                        <span class="step-pill-status">{{ $step3Completed ? 'Concluído' : ($step3Clickable ? 'Pendente' : 'Bloqueado') }}</span>
                    </div>
                @if($step3Clickable) </a> @else </div> @endif
</div>

{{-- Seta 3->4 --}}
<div class="step-pill-connector">
    <div class="stepper-arrow {{ $step3Completed ? 'arrow-active' : '' }}">
        <i class="fas fa-chevron-right"></i>
        <i class="fas fa-chevron-right arrow-second"></i>
    </div>
</div>

{{-- STEP 4 --}}
<div class="step-pill-item {{ $currentStep == 4 ? 'is-active' : '' }} {{ !$step4Clickable ? 'is-locked' : '' }}">
    @if($step4Clickable)
        <a href="{{ route('inclusive-radar.barriers.stage4', $barrier) }}" class="step-pill-link {{ $step4Completed ? 'is-completed' : '' }}">
            @else
                <div class="step-pill-link">
                    @endif
                    <div class="step-pill-icon">
                        @if($step4Completed) <i class="fas fa-check"></i> @else <span>4</span> @endif
                    </div>
                    <div class="step-pill-text">
                        <span class="step-pill-title">Etapa 4 - Resolvida</span>
                        <span class="step-pill-status">{{ $step4Completed ? 'Concluído' : ($step4Clickable ? 'Pendente' : 'Bloqueado') }}</span>
                    </div>
                @if($step4Clickable) </a> @else </div> @endif
</div>

</div>
</div>

<style>
    /* Estilos unificados baseados no padrão Maintenance */
    .stepper-pill-container {
        background: #ffffff;
        border: 1px solid #e0e4f1;
        border-radius: 999px;
        padding: 0.5rem 1rem;
        margin: 0 auto;
        max-width: 1000px;
        display: flex;
        align-items: center;
    }

    .step-pill-item {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .step-pill-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 15px;
        border-radius: 999px;
        text-decoration: none !important;
        transition: all 0.3s ease;
    }

    .step-pill-item.is-active .step-pill-link {
        background: rgba(108, 92, 231, 0.08);
        border: 1px solid rgba(108, 92, 231, 0.2);
    }

    .step-pill-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        background: #f8f9fa;
        color: #9aa3c0;
        border: 1px solid #e0e4f1;
    }

    .is-active .step-pill-icon {
        background: #6c5ce7;
        color: white;
        border-color: #6c5ce7;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.15);
    }

    .is-completed .step-pill-icon {
        background: #2ecc71 !important;
        color: white !important;
        border-color: #2ecc71 !important;
    }

    .step-pill-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #4b5563;
        display: block;
    }

    .is-active .step-pill-title { color: #6c5ce7; }

    .step-pill-status {
        font-size: 0.65rem;
        color: #9aa3c0;
        text-transform: uppercase;
    }

    .step-pill-connector { flex: 0 0 40px; text-align: center; }

    .stepper-arrow { color: #e0e4f1; font-size: 0.9rem; }
    .stepper-arrow.arrow-active {
        color: #6c5ce7;
        animation: arrowMoving 2s infinite;
    }

    @keyframes arrowMoving {
        0%, 100% { transform: translateX(0); opacity: 0.5; }
        50% { transform: translateX(5px); opacity: 1; }
    }

    .is-locked { opacity: 0.5; cursor: not-allowed; }
    .is-locked .step-pill-link { pointer-events: none; }

    @media (max-width: 992px) {
        .stepper-pill-container { border-radius: 20px; flex-direction: column; padding: 15px; }
        .step-pill-connector { transform: rotate(90deg); height: 30px; }
    }
</style>
