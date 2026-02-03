@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Recurso de Acessibilidade</h2>
            <p class="text-muted">Atualizando informações de: <strong>{{ $accessibilityFeature->name }}</strong></p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.accessibility-features.update', $accessibilityFeature->id) }}" method="POST">
            @method('PUT')

            <x-forms.section title="Identificação do Recurso" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Nome do Recurso *"
                    required
                    :value="old('name', $accessibilityFeature->name)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição Detalhada"
                    rows="4"
                    :value="old('description', $accessibilityFeature->description)"
                />
            </div>

            <x-forms.section title="Configurações de Status" />

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Recurso Ativo"
                    description="Define se este recurso está disponível para uso no sistema"
                    :checked="old('is_active', $accessibilityFeature->is_active)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.accessibility-features.index') }}" variant="secondary">
                    Cancelar Alterações
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Alterações
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
