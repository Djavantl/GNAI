@extends('layouts.master')

@section('title', 'Adaptar Conteúdo')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Plano #' . $pei->id => route('specialized-educational-support.pei.show', $pei->id),
            'Adaptar Conteúdo' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Conteúdo Programático Adaptado</h2>
            <p class="text-muted">Registre quais tópicos da ementa sofrerão ajustes de acessibilidade curricular.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei.content.store', $pei->id) }}" method="POST">
            @csrf
            <input type="hidden" name="pei_id" value="{{ $pei->id }}">

            <x-forms.section title="Identificação do Conteúdo" />

            <div class="col-md-12">
                <x-forms.input 
                    name="title" 
                    label="Título do Conteúdo / Tópico " 
                    required 
                    placeholder="Ex: Geometria Analítica - Estudo da Reta"
                    :value="old('title')" 
                />
            </div>

            <div class="col-md-12 mt-3">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição da Adaptação Curricular" 
                    rows="5" 
                    placeholder="Descreva como este conteúdo será apresentado ou reduzido para garantir a aprendizagem."
                    :value="old('description')" 
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