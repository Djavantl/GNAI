@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Tipo de Recurso</h2>
            <p class="text-muted">Modificando as configurações da categoria: <strong>{{ $resourceType->name }}</strong></p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">Identificador</span>
            <span class="badge bg-purple-dark fs-6" style="background-color: #4c1d95;">ID #{{ $resourceType->id }}</span>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <p class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Verifique os erros abaixo:</p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.resource-types.update', $resourceType->id) }}" method="POST">
            @csrf
            @method('PUT')

            <x-forms.section title="Identificação da Categoria" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Nome do Tipo *"
                    required
                    :value="old('name', $resourceType->name)"
                    placeholder="Ex: Teclados Adaptados, Softwares de Leitura..."
                />
            </div>

            <x-forms.section title="Regras e Natureza do Recurso" />

            {{-- Coluna: Aplicação --}}
            <div class="col-md-6 mb-4">
                <div class="p-3 border rounded bg-light h-100">
                    <label class="form-label fw-bold text-purple-dark mb-3 text-uppercase small">Este tipo se aplica a:</label>
                    <div class="d-flex flex-column gap-2">
                        {{-- Hidden inputs para garantir envio do valor 0 quando desmarcado --}}
                        <input type="hidden" name="for_assistive_technology" value="0">
                        <x-forms.checkbox
                            name="for_assistive_technology"
                            label="Tecnologias Assistivas"
                            :checked="old('for_assistive_technology', $resourceType->for_assistive_technology)"
                        />

                        <input type="hidden" name="for_educational_material" value="0">
                        <x-forms.checkbox
                            name="for_educational_material"
                            label="Materiais Didáticos"
                            :checked="old('for_educational_material', $resourceType->for_educational_material)"
                        />
                    </div>
                </div>
            </div>

            {{-- Coluna: Natureza Digital --}}
            <div class="col-md-6 mb-4">
                <div class="p-3 border rounded h-100" style="background-color: #f0f7ff; border-color: #cfe2ff !important;">
                    <label class="form-label fw-bold text-primary mb-3 text-uppercase small italic">Natureza do Recurso:</label>
                    <input type="hidden" name="is_digital" value="0">
                    <x-forms.checkbox
                        name="is_digital"
                        label="Este recurso é digital?"
                        description="Afeta se o sistema pedirá controle de estoque físico (patrimônio) ou não."
                        :checked="old('is_digital', $resourceType->is_digital)"
                    />
                </div>
            </div>

            <x-forms.section title="Status de Ativação" />

            <div class="col-md-12">
                <div class="p-3 border rounded border-success bg-light">
                    <input type="hidden" name="is_active" value="0">
                    <x-forms.checkbox
                        name="is_active"
                        id="is_active"
                        label="Tipo de Recurso Ativo no Sistema"
                        description="Se desativado, esta categoria não poderá ser selecionada em novos cadastros."
                        :checked="old('is_active', $resourceType->is_active)"
                    />
                </div>
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.resource-types.index') }}" variant="secondary">
                    Cancelar Alterações
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Alterações
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
