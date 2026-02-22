@extends('layouts.master')

@section('title', 'Manutenção - Etapa 1')

@section('content')
    {{-- Lógica de bloqueio --}}
    @php
        $isLocked = (bool) $stage1?->completed_at;
    @endphp

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Manutenções' => route('inclusive-radar.maintenances.index'),
            'Manutenção ' . $maintenance->maintainable->name => route('inclusive-radar.maintenances.show', $maintenance),
            'Etapa 1' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Etapa 1 – {{ $isLocked ? 'Dados preenchidos' : 'Preencher dados iniciais' }}</h2>
            <p class="text-muted mb-0">
                {{ $isLocked
                    ? 'Esta etapa foi finalizada e os dados estão bloqueados para alteração.'
                    : 'Preencha as informações sobre a manutenção antes de concluir esta etapa.' }}
            </p>
        </header>
        <div>
            <x-buttons.link-button
                :href="route('inclusive-radar.maintenances.show', $maintenance)"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mb-4">
        @include('pages.inclusive-radar.maintenances.partials.maintenance-stepper', ['maintenance' => $maintenance])
    </div>

    @if($isLocked)
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <i class="fas fa-lock me-2"></i> <strong>Etapa Concluída:</strong> Os campos abaixo estão em modo de apenas leitura.
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card
            action="{{ $isLocked ? '#' : route('inclusive-radar.maintenances.saveStage1', $maintenance) }}"
            method="POST"
        >
            @csrf
            @method('PATCH')

            <input type="hidden" name="maintenance_id" value="{{ $maintenance->id }}">
            <input type="hidden" name="step_number" value="1">

            <x-forms.section title="Informações da Manutenção" />

            <div class="col-md-6">
                <x-forms.input
                    name="estimated_cost"
                    label="Custo Estimado"
                    type="text"
                    placeholder="0,00"
                    class="money"
                    :value="old('estimated_cost', isset($stage1?->estimated_cost) ? number_format($stage1->estimated_cost, 2, ',', '.') : '')"
                    :readonly="$isLocked"
                />
            </div>

            <div class="col-md-6"></div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="damage_description"
                    label="Descrição do Dano"
                    rows="3"
                    placeholder="Descreva o problema identificado no recurso"
                    :value="old('damage_description', $stage1?->damage_description)"
                    :readonly="$isLocked"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="observation"
                    label="Observações Adicionais"
                    rows="2"
                    placeholder="Alguma nota importante sobre este processo?"
                    :value="old('observation', $stage1?->observation)"
                    :readonly="$isLocked"
                />
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4">
                @if(!$isLocked)
                    <x-buttons.submit-button
                        type="submit"
                        name="finalize"
                        value="0"
                        variant="secondary"
                        class="btn-action"
                    >
                        <i class="fas fa-save me-1"></i> Salvar rascunho
                    </x-buttons.submit-button>

                    <x-buttons.submit-button
                        type="submit"
                        name="finalize"
                        value="1"
                        class="btn-action new submit"
                    >
                        <i class="fas fa-check-circle me-1"></i> Prosseguir para Etapa 2
                    </x-buttons.submit-button>
                @else
                    <x-buttons.link-button
                        :href="route('inclusive-radar.maintenances.stage2', $maintenance)"
                        variant="primary"
                        class="btn-action new submit"
                    >
                        Ir para Etapa 2 <i class="fas fa-arrow-right ms-1"></i>
                    </x-buttons.link-button>
                @endif
            </div>
        </x-forms.form-card>
    </div>
@endsection
