@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Deficiência</h2>
            <p class="text-muted">Atualizando informações de: <strong>{{ $deficiency->name }}</strong></p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.deficiencies.update', $deficiency) }}" method="POST">
            @method('PUT')
            
            <x-forms.section title="Atualizar Dados" />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome da Deficiência *" 
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

            <div class="col-md-6">
                <x-forms.input 
                    name="code" 
                    label="Código Interno (Sistema)" 
                    :value="old('code', $deficiency->code)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="is_active"
                    label="Status *"
                    required
                    :options="['1' => 'Ativa', '0' => 'Inativa']"
                    :value="old('is_active', $deficiency->is_active)"
                    :selected="old('is_active', $deficiency->is_active)"
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

            <div class="col-12 d-flex justify-content-between border-t pt-4 px-4 pb-4">
                <div>
                    <x-buttons.link-button href="{{ route('specialized-educational-support.deficiencies.index') }}" variant="secondary">
                        Voltar
                    </x-buttons.link-button>
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                        <i class="fas fa-sync mr-2"></i> Salvar Alterações
                    </x-buttons.submit-button>
                </div>
            </div>

        </x-forms.form-card>
    </div>
@endsection