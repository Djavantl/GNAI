@extends('layouts.master')

@section('title', 'Visualizar Objetivo do PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student_id),
            'Plano #' . $pei->id => route('specialized-educational-support.pei.show', $pei->id),
            'Objetivo #' . $specific_objective->id => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Detalhes do Objetivo Específico</h2>
            <p class="text-muted">Informações detalhadas sobre a meta de aprendizagem.</p>
        </div>

        <div class="d-flex gap-2">
            @if(!$pei->is_finished)
                <x-buttons.link-button 
                    :href="route('specialized-educational-support.pei.objective.edit', $specific_objective)" 
                    variant="warning">
                    <i class="fas fa-edit"></i> Editar
                </x-buttons.link-button>
            @endif

            <x-buttons.link-button 
                :href="route('specialized-educational-support.pei.show', $pei->id)" 
                variant="secondary">
                <i class="fas fa-arrow-left"></i>Voltar para o Plano
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white">
        <div class="row g-0">
            
            {{-- ================= INFORMAÇÕES GERAIS ================= --}}
            <x-forms.section title="Detalhamento da Meta" />

            <x-show.info-item 
                label="Descrição do Objetivo" 
                :value="$specific_objective->description" 
                column="col-md-12" 
                isBox="true" 
            />

            <x-show.info-item 
                label="Status Atual" 
                column="col-md-6" 
                isBox="true">
                {{ $specific_objective->status->label() }}
            </x-show.info-item>

            <x-show.info-item 
                label="Disciplina Relacionada" 
                :value="$pei->discipline->name" 
                column="col-md-6" 
                isBox="true" 
            />

            {{-- ================= ACOMPANHAMENTO ================= --}}
            <x-forms.section title="Acompanhamento e Progresso" />

            <x-show.info-item 
                label="Observações de Progresso" 
                :value="$specific_objective->observations_progress ?? 'Nenhuma observação registrada até o momento.'" 
                column="col-md-12" 
                isBox="true" 
            />

            <div class="col-md-6 px-4 py-3">
                <small class="text-muted d-block">Criado em:</small>
                <span class="fw-bold">{{ $specific_objective->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="col-md-6 px-4 py-3">
                <small class="text-muted d-block">Última Atualização:</small>
                <span class="fw-bold">{{ $specific_objective->updated_at->format('d/m/Y H:i') }}</span>
            </div>

            {{-- ================= RODAPÉ / AÇÕES ================= --}}
            <div class="col-12 border-top p-4 d-flex justify-content-end">
                @if(!$pei->is_finished)
                    <form method="POST" 
                          action="{{ route('specialized-educational-support.pei.objective.destroy', $specific_objective->id) }}"
                          onsubmit="return confirm('Tem certeza que deseja excluir este objetivo?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                             <i class="fas fa-trash"></i> Excluir Objetivo
                        </x-buttons.submit-button>
                    </form>
                @endif
            </div>

        </div>
    </div>
@endsection