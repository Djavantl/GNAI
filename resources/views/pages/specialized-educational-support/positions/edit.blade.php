@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cargos' => route('specialized-educational-support.positions.index'),
            $position->name => route('specialized-educational-support.positions.show', $position),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Cargo</h2>
            <p class="text-muted">Alterando informações do cargo: <strong>{{ $position->name }}</strong></p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.positions.update', $position) }}" method="POST">
            @method('PUT')
            
            <x-forms.section title="Atualizar Dados" />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome do Cargo *" 
                    required 
                    :value="old('name', $position->name)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="is_active"
                    label="Status *"
                    required
                    :options="['1' => 'Ativo', '0' => 'Inativo']"
                    :value="old('is_active', $position->is_active)"
                    :selected="old('is_active', $position->is_active)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição" 
                    rows="3" 
                    :value="old('description', $position->description)" 
                />
            </div>

            <div class="col-12 d-flex justify-content-between border-t pt-4 px-4 pb-4">
                <div>
                    <x-buttons.link-button href="{{ route('specialized-educational-support.positions.index') }}" variant="secondary">
                        Voltar
                    </x-buttons.link-button>
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                        <i class="fas fa-sync mr-2"></i> Atualizar Cargo
                    </x-buttons.submit-button>
                </div>
            </div>

        </x-forms.form-card>
    </div>
@endsection