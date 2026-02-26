@extends('layouts.master')

@section('title', 'Editar Metodologia do PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student_id),
            'Plano #' . $pei->id => route('specialized-educational-support.pei.show', $pei->id),
            'Metodologia #' . $methodology->id => route('specialized-educational-support.pei.methodology.show', $methodology),
            'Editar Metodologia' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Metodologia</h2>
            <p class="text-muted">Ajustar estratégias e recursos para {{ $pei->student->person->name }}.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei.methodology.update', $methodology) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="pei_id" value="{{ $pei->id }}">

            <x-forms.section title="Estratégias e Recursos" />

            <div class="col-md-12">
                <x-forms.input 
                    name="title" 
                    label="Título da Metodologia" 
                    required 
                    placeholder="Ex: Equações de 2º Grau"
                    :value="old('title', $methodology->title)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição da Metodologia" 
                    rows="3" 
                    required 
                    placeholder="Ex: Uso de metodologias ativas com foco em aprendizado visual..."
                    :value="old('description', $methodology->description)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="resources_used" 
                    label="Recursos Utilizados" 
                    rows="3" 
                    placeholder="Ex: Software de leitura, lupas, material dourado, etc."
                    :value="old('resources_used', $methodology->resources_used)" 
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