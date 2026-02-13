@extends('layouts.master')

@section('title', 'Editar Objetivo do PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student_id),
            'Plano #' . $pei->id => route('specialized-educational-support.pei.show', $pei->id),
            'Objetivo #' . $specific_objective->id => route('specialized-educational-support.pei.objective.show', $specific_objective),
            'Editar Objetivo' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Objetivo Específico</h2>
            <p class="text-muted">Editar metas de aprendizagem claras e mensuráveis para a disciplina de {{ $pei->discipline->name }}.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei.objective.update', $specific_objective) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="pei_id" value="{{ $pei->id }}">

            <x-forms.section title="Detalhamento da Meta" />

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição do Objetivo" 
                    rows="3" 
                    required 
                    placeholder="Ex: Identificar e aplicar fórmulas de equações do 2º grau com auxílio de material concreto."
                    :value="old('description', $specific_objective->description)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status"
                    label="Status *"
                    required
                    :options="$statuses"
                    :selected="old('status', $specific_objective->status->value)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="observations_progress" 
                    label="Observações de Progresso" 
                    rows="3" 
                    placeholder="Espaço para anotações sobre o desenvolvimento inicial deste objetivo."
                    :value="old('observations_progress', $specific_objective->observations_progress)" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei->id) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection