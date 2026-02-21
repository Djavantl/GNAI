@extends('layouts.master')

@section('title', "Editar - $resourceStatus->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Status dos Recursos' => route('inclusive-radar.resource-statuses.index'),
            $resourceStatus->name => route('inclusive-radar.resource-statuses.show', $resourceStatus),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Status do Recurso</h2>
            <p class="text-muted">Modifique as regras de negócio e visibilidade para o status: <strong>{{ $resourceStatus->name }}</strong></p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.resource-statuses.show', $resourceStatus) }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.resource-statuses.update', $resourceStatus) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- SEÇÃO 1: Identificação --}}
            <x-forms.section title="Identificação do Status" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Nome exibido *"
                    required
                    :value="old('name', $resourceStatus->name)"
                    placeholder="Ex: Disponível, Em Manutenção, Extraviado..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição / Finalidade"
                    rows="3"
                    :value="old('description', $resourceStatus->description)"
                    placeholder="Explique quando este status deve ser aplicado..."
                />
            </div>

            {{-- SEÇÃO 2: Regras de Negócio (Bloqueios) --}}
            <x-forms.section title="Regras de Bloqueio" />

            <div class="col-md-6">
                <x-forms.checkbox
                    name="blocks_loan"
                    label="Bloquear Empréstimo"
                    description="Recursos com este status não poderão ser emprestados"
                    :checked="old('blocks_loan', $resourceStatus->blocks_loan)"
                />
            </div>

            <div class="col-md-6 mb-3">
                <x-forms.checkbox
                    name="blocks_access"
                    label="Bloquear Acesso"
                    description="Oculta o recurso de consultas gerais no sistema"
                    :checked="old('blocks_access', $resourceStatus->blocks_access)"
                />
            </div>

            {{-- SEÇÃO 3: Aplicabilidade e Ativação --}}
            <x-forms.section title="Aplicabilidade e Ativação" />

            {{-- Status Ativo: Estilo Limpo TA --}}
            <div class="col-md-12 mb-3">
                <x-forms.checkbox
                    name="is_active"
                    label="Status Ativo no Sistema"
                    description="Se desativado, este status não aparecerá em novos cadastros ou edições"
                    :checked="old('is_active', $resourceStatus->is_active)"
                />
            </div>

            {{-- Aplicabilidade: Estilo Público-alvo TA --}}
            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold text-purple-dark">Este tipo se aplica a: *</label>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    <x-forms.checkbox
                        name="for_assistive_technology"
                        label="Tecnologias Assistivas"
                        :checked="old('for_assistive_technology', $resourceStatus->for_assistive_technology)"
                        class="mb-0"
                    />
                    <x-forms.checkbox
                        name="for_educational_material"
                        label="Materiais Pedagógicos Acessíveis"
                        :checked="old('for_educational_material', $resourceStatus->for_educational_material)"
                        class="mb-0"
                    />
                </div>
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.resource-statuses.show', $resourceStatus) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
