@extends('layouts.master')

@section('title', 'Editar Conteúdo do PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student_id),
            'Plano #' . $pei->id => route('specialized-educational-support.pei.show', $pei->id),
            'Conteúdo #' . $content_programmatic->id => route('specialized-educational-support.pei.content.show', $content_programmatic),
            'Editar Conteúdo' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Conteúdo Programático</h2>
            <p class="text-muted">Editar o conteúdo adaptado para a disciplina de {{ $pei->discipline->name }}.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei.content.update', $content_programmatic) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="pei_id" value="{{ $pei->id }}">

            <x-forms.section title="Detalhamento do Conteúdo" />

            <div class="col-md-12">
                <x-forms.input 
                    name="title" 
                    label="Título do Conteúdo" 
                    required 
                    placeholder="Ex: Equações de 2º Grau"
                    :value="old('title', $content_programmatic->title)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição Adaptada" 
                    rows="4" 
                    placeholder="Descreva como o conteúdo será abordado de forma adaptada."
                    :value="old('description', $content_programmatic->description)" 
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