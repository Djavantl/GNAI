@extends('layouts.master')

@section('title', "Editar - $typeAttribute->label")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Atributos de Recursos' => route('inclusive-radar.type-attributes.index'),
            $typeAttribute->label => route('inclusive-radar.type-attributes.show', $typeAttribute),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Atributo</h2>
            <p class="text-muted">Atualizando as definições do campo: <strong>{{ $typeAttribute->label }}</strong></p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.type-attributes.show', $typeAttribute) }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.type-attributes.update', $typeAttribute->id) }}" method="POST">
            @csrf
            @method('PUT')

            <x-forms.section title="Identificação do Atributo" />

            {{-- Label --}}
            <div class="col-md-12">
                <x-forms.input
                    name="label"
                    label="Rótulo de Exibição (Label)"
                    required
                    :value="old('label', $typeAttribute->label)"
                    placeholder="Ex: Versão do Software"
                />
            </div>

            {{-- Nome Técnico (Slug) com Aviso --}}
            <div class="col-md-6 mb-4">
                <x-forms.input
                    name="name"
                    label="Nome Técnico (Identificador)"
                    readonly
                    :value="old('name', $typeAttribute->name)"
                    style=" cursor: not-allowed; pointer-events: none;"
                />
                <div class="mt-2 p-2 border-start border-warning bg-light" style="border-width: 4px !important;">
                    <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.4;">
                        <span class="text-warning fw-bold">Aviso:</span> Alterar o rótulo acima não mudará este nome técnico.
                        Isso acontece para preservar o vínculo com os dados já cadastrados.
                        Caso precise alterar o identificador, você deve <strong>excluir</strong> este atributo e criar um novo.
                    </p>
                </div>
            </div>

            {{-- Tipo de Dado --}}
            <div class="col-md-6">
                <x-forms.select
                    name="field_type"
                    label="Tipo de Dado"
                    required
                    :options="[
                        'string' => 'Texto Curto (String)',
                        'text' => 'Texto Longo (TextArea)',
                        'integer' => 'Número Inteiro',
                        'decimal' => 'Número Decimal',
                        'boolean' => 'Sim/Não (Booleano)',
                        'date' => 'Data'
                    ]"
                    :selected="old('field_type', $typeAttribute->field_type)"
                />
            </div>

            <x-forms.section title="Configurações e Visibilidade" />

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_required"
                    id="is_required"
                    label="Campo Obrigatório"
                    description="O sistema exigirá este valor ao salvar o recurso"
                    :checked="old('is_required', $typeAttribute->is_required)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    id="is_active"
                    label="Ativar Atributo"
                    description="Fica disponível para uso nos formulários imediatamente"
                    :checked="old('is_active', $typeAttribute->is_active)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.type-attributes.show', $typeAttribute) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
