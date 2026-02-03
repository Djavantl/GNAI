@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Status do Recurso</h2>
            <p class="text-muted">Modifique as regras de negócio e visibilidade para o status: <strong>{{ $resourceStatus->name }}</strong></p>
        </div>
        <div class="align-self-center text-end">
            <span class="d-block text-muted small uppercase fw-bold">Código do Sistema</span>
            <span class="badge bg-purple-dark fs-6" style="background-color: #4c1d95;">{{ $resourceStatus->code }}</span>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

            <div class="col-md-6">
                <x-forms.checkbox
                    name="blocks_access"
                    label="Bloquear Acesso"
                    description="Oculta o recurso de consultas gerais no sistema"
                    :checked="old('blocks_access', $resourceStatus->blocks_access)"
                />
            </div>

            {{-- SEÇÃO 3: Aplicabilidade e Visibilidade --}}
            <x-forms.section title="Aplicabilidade e Ativação" />

            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold text-purple-dark">Disponível para os módulos:</label>
                <div class="d-flex gap-4 p-3 border rounded bg-light">
                    <x-forms.checkbox
                        name="for_assistive_technology"
                        label="Tecnologia Assistiva"
                        :checked="old('for_assistive_technology', $resourceStatus->for_assistive_technology)"
                    />
                    <x-forms.checkbox
                        name="for_educational_material"
                        label="Material Pedagógico"
                        :checked="old('for_educational_material', $resourceStatus->for_educational_material)"
                    />
                </div>
            </div>

            <div class="col-md-12">
                <div class="p-3 border rounded border-info bg-light">
                    <x-forms.checkbox
                        name="is_active"
                        label="Status Ativo no Sistema"
                        description="Se desativado, este status não aparecerá em novos cadastros ou edições"
                        :checked="old('is_active', $resourceStatus->is_active)"
                    />
                </div>
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.resource-statuses.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Atualizar Status
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
