@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Atributo Personalizado</h2>
            <p class="text-muted">Crie campos específicos que serão preenchidos nos detalhes de cada recurso.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.type-attributes.store') }}" method="POST">
            @csrf

            <x-forms.section title="Identificação do Atributo" />

            {{-- Campo Label --}}
            <div class="col-md-12">
                <x-forms.input
                    id="input_label"
                    name="label"
                    label="Rótulo de Exibição (Label) *"
                    required
                    :value="old('label')"
                    placeholder="Ex: Versão do Software"
                />
            </div>

            {{-- Campo Name Técnico --}}
            <div class="col-md-6">
                <x-forms.input
                    id="input_name"
                    name="name"
                    label="Nome Técnico (Automático)"
                    required
                    readonly
                    :value="old('name')"
                    {{-- Adicionei o pointer-events none para reforçar que não é clicável --}}
                    style="background-color: #f8f9fa; cursor: not-allowed; pointer-events: none;"
                />
                <small class="text-muted">Gerado automaticamente a partir do rótulo.</small>
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="field_type"
                    label="Tipo de Dado *"
                    required
                    :options="[
                        'string' => 'Texto Curto (String)',
                        'text' => 'Texto Longo (TextArea)',
                        'integer' => 'Número Inteiro',
                        'decimal' => 'Número Decimal',
                        'boolean' => 'Sim/Não (Booleano)',
                        'date' => 'Data'
                    ]"
                    :selected="old('field_type')"
                />
            </div>

            <x-forms.section title="Configurações e Visibilidade" />

            {{-- Checklist de Configurações no estilo TA --}}
            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_required"
                    id="is_required"
                    label="Campo Obrigatório"
                    description="O sistema exigirá este valor ao salvar o recurso"
                    :checked="old('is_required')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    id="is_active"
                    label="Ativar Atributo"
                    description="Fica disponível para uso nos formulários imediatamente"
                    :checked="old('is_active', true)"
                />
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.type-attributes.index') }}" variant="secondary">
                    Voltar para Listagem
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Atributo
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
