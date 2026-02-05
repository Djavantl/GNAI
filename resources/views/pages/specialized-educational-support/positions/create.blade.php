@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Novo Cargo</h2>
            <p class="text-muted">Defina as atribuições e o status do cargo no sistema.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.positions.store') }}" method="POST">
            
            <x-forms.section title="Informações do Cargo" />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome do Cargo *" 
                    required 
                    placeholder="Ex: Professor AEE, Psicólogo..."
                    :value="old('name')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="is_active"
                    label="Status *"
                    required
                    :options="['1' => 'Ativo', '0' => 'Inativo']"
                    :value="old('is_active', '1')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição" 
                    rows="3" 
                    placeholder="Breve descrição das responsabilidades..."
                    :value="old('description')" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.positions.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar Cargo
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection