@extends('layouts.master')

@section('title', 'Nova Metodologia / Recurso')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Plano #' . $pei->id => route('specialized-educational-support.pei.show', $pei->id),
            'Nova Metodologia' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Metodologia e Recursos de Acessibilidade</h2>
            <p class="text-muted">Defina as estratégias didáticas e recursos técnicos para este plano.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei.methodology.store', $pei->id) }}" method="POST">
            @csrf
            <input type="hidden" name="pei_id" value="{{ $pei->id }}">

            <x-forms.section title="Estratégia Pedagógica" />

            <div class="col-md-6">
                <x-forms.input 
                    name="title" 
                    label="Título da Metodologia" 
                    required 
                    placeholder="Ex: Geometria Analítica - Estudo da Reta"
                    :value="old('title')" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição da Metodologia " 
                    rows="4" 
                    required 
                    placeholder="Ex: Utilização de mapas mentais, tempo estendido para avaliações e mediação constante."
                    :value="old('description')" 
                />
            </div>

            <x-forms.section title="Materiais de Apoio" />

            <div class="col-md-12">
                <x-forms.input 
                    name="resources_used" 
                    label="Recursos / Tecnologias Assistivas" 
                    placeholder="Ex: Software de leitura de tela, material em Braille, reglete, prancha de comunicação."
                    :value="old('resources_used')" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei->id) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection