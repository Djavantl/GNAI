@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Deficiências' => route('specialized-educational-support.deficiencies.index'),
            $deficiency->name => route('specialized-educational-support.deficiencies.show', $deficiency),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Deficiência</h2>
            <p class="text-muted">Atualizando informações de: <strong>{{ $deficiency->name }}</strong></p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.deficiencies.show', $deficiency) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.deficiencies.update', $deficiency) }}" method="POST">
            @method('PUT')
            
            <x-forms.section title="Atualizar Dados" />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome da Deficiência " 
                    required 
                    :value="old('name', $deficiency->name)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="cid_code" 
                    label="Código CID" 
                    :value="old('cid_code', $deficiency->cid_code)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição / Detalhes" 
                    rows="3" 
                    :value="old('description', $deficiency->description)" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end border-t pt-4 px-4 pb-4">
                <div>
                    <x-buttons.link-button class="me-3" href="{{ route('specialized-educational-support.deficiencies.show', $deficiency) }}" variant="secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </x-buttons.link-button>
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.submit-button type="submit" class="btn-action new submit">
                        <i class="fas fa-save"></i> Salvar
                    </x-buttons.submit-button>
                </div>
            </div>

        </x-forms.form-card>
    </div>
@endsection