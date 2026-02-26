@php
    $barrier = $barrier ?? null;

    $step1Completed = $barrier && $barrier->step_number >= 1;
    $step2Completed = $barrier && $barrier->step_number >= 2;
    $step3Completed = $barrier && $barrier->step_number >= 3;
    $step4Completed = $barrier && $barrier->step_number >= 4;

    $isClosedOrNotApplicable = $barrier?->isClosedOrNotApplicable() ?? false;

    $step2Clickable = $barrier && $step1Completed && !$isClosedOrNotApplicable;
    $step3Clickable = $barrier && $step2Completed && !$isClosedOrNotApplicable;
    $step4Clickable = $barrier && $step3Completed && !$isClosedOrNotApplicable;

    $currentStep = $barrier ? $barrier->step_number : 1;
@endphp

<div class="barrier-stepper-wrapper mb-5">
    <div class="stepper-pill-container d-flex align-items-center justify-content-between position-relative shadow-sm">

        {{-- STEP 1 --}}
        <div class="step-pill-item {{ $currentStep == 1 ? 'is-active' : '' }}">
            <a href="#" class="step-pill-link {{ $step1Completed ? 'is-completed' : '' }}">
                <div class="step-pill-icon">
                    @if($step1Completed)
                        <i class="fas fa-check"></i>
                    @else
                        <span>1</span>
                    @endif
                </div>
                <div class="step-pill-text">
                    <span class="step-pill-title">Criação</span>
                    <span class="step-pill-status">{{ $step1Completed ? 'Concluído' : 'Em progresso' }}</span>
                </div>
            </a>
        </div>

        {{-- SETA 1->2 --}}
        <div class="step-pill-connector">
            <div class="stepper-arrow {{ $step1Completed ? 'arrow-active' : '' }}">
                <i class="fas fa-chevron-right"></i>
                <i class="fas fa-chevron-right arrow-second"></i>
            </div>
        </div>

        {{-- STEP 2 --}}
        <div class="step-pill-item {{ $currentStep == 2 ? 'is-active' : '' }} {{ !$step2Clickable ? 'is-locked' : '' }}">
            @if($step2Clickable)
                <a href="#" class="step-pill-link {{ $step2Completed ? 'is-completed' : '' }}">
                    @else
                        <div class="step-pill-link">
                            @endif
                            <div class="step-pill-icon">
                                @if($step2Completed)
                                    <i class="fas fa-check"></i>
                                @else
                                    <span>2</span>
                                @endif
                            </div>
                            <div class="step-pill-text">
                                <span class="step-pill-title">Análise</span>
                                <span class="step-pill-status">
                        {{ $step2Completed ? 'Concluído' : ($step2Clickable ? 'Pendente' : 'Bloqueado') }}
                    </span>
                            </div>
                        @if($step2Clickable)
                </a>
            @else
        </div>
        @endif
    </div>

    {{-- SETA 2->3 --}}
    <div class="step-pill-connector">
        <div class="stepper-arrow {{ $step2Completed ? 'arrow-active' : '' }}">
            <i class="fas fa-chevron-right"></i>
            <i class="fas fa-chevron-right arrow-second"></i>
        </div>
    </div>

    {{-- STEP 3 --}}
    <div class="step-pill-item {{ $currentStep == 3 ? 'is-active' : '' }} {{ !$step3Clickable ? 'is-locked' : '' }}">
        @if($step3Clickable)
            <a href="#" class="step-pill-link {{ $step3Completed ? 'is-completed' : '' }}">
                @else
                    <div class="step-pill-link">
                        @endif
                        <div class="step-pill-icon">
                            @if($step3Completed)
                                <i class="fas fa-check"></i>
                            @else
                                <span>3</span>
                            @endif
                        </div>
                        <div class="step-pill-text">
                            <span class="step-pill-title">Plano de Ação</span>
                            <span class="step-pill-status">
                        {{ $step3Completed ? 'Concluído' : ($step3Clickable ? 'Pendente' : 'Bloqueado') }}
                    </span>
                        </div>
                    @if($step3Clickable)
            </a>
        @else
    </div>
    @endif
</div>

{{-- SETA 3->4 --}}
<div class="step-pill-connector">
    <div class="stepper-arrow {{ $step3Completed ? 'arrow-active' : '' }}">
        <i class="fas fa-chevron-right"></i>
        <i class="fas fa-chevron-right arrow-second"></i>
    </div>
</div>

{{-- STEP 4 --}}
<div class="step-pill-item {{ $currentStep == 4 ? 'is-active' : '' }} {{ !$step4Clickable ? 'is-locked' : '' }}">
    @if($step4Clickable)
        <a href="#" class="step-pill-link {{ $step4Completed ? 'is-completed' : '' }}">
            @else
                <div class="step-pill-link">
                    @endif
                    <div class="step-pill-icon">
                        @if($step4Completed)
                            <i class="fas fa-check"></i>
                        @else
                            <span>4</span>
                        @endif
                    </div>
                    <div class="step-pill-text">
                        <span class="step-pill-title">Resolução</span>
                        <span class="step-pill-status">
                        {{ $step4Completed ? 'Concluído' : ($step4Clickable ? 'Pendente' : 'Bloqueado') }}
                    </span>
                    </div>
                @if($step4Clickable)
        </a>
    @else
</div>
@endif
</div>

</div>
</div>

<style>
    /* ===== Estilo idêntico ao stepper de manutenção ===== */
    .stepper-pill-container {
        background: #ffffff;
        border: 1px solid #e0e4f1;
        border-radius: 999px;
        padding: 0.5rem 1.5rem;
        display: flex;
        align-items: center;
        max-width: 1000px;
        margin: 0 auto;
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

    .step-pill-item.is-active .step-pill-link {
        background: rgba(108, 92, 231, 0.08);
        border-color: rgba(108, 92, 231, 0.2);
    }

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

    .is-completed .step-pill-icon {
        background: #2ecc71 !important;
        color: white !important;
        border-color: #2ecc71 !important;
    }

    .is-active .step-pill-icon {
        background: #6c5ce7;
        color: white;
        border-color: #6c5ce7;
        box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.15);
    }

    .step-pill-text {
        display: flex;
        flex-direction: column;
    }

    .step-pill-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #4b5563;
    }

    .is-active .step-pill-title { color: #6c5ce7; }

    .step-pill-status {
        font-size: 0.7rem;
        color: #9aa3c0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

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
        color: #6c5ce7;
        filter: drop-shadow(0 0 3px rgba(108, 92, 231, 0.3));
        animation: arrowMoving 2s infinite;
    }

    .arrow-second { margin-left: -5px; opacity: 0.5; }

    @keyframes arrowMoving {
        0% { transform: translateX(0); opacity: 0.5; }
        50% { transform: translateX(5px); opacity: 1; }
        100% { transform: translateX(0); opacity: 0.5; }
    }

    .is-locked { opacity: 0.5; cursor: not-allowed !important; }
    .is-locked .step-pill-link { pointer-events: none; }

    @media (max-width: 768px) {
        .stepper-pill-container { flex-direction: column; border-radius: 20px; padding: 15px; gap: 10px; }
        .step-pill-connector { transform: rotate(90deg); height: 30px; }
        .step-pill-item { width: 100%; justify-content: flex-start; }
    }
</style>
