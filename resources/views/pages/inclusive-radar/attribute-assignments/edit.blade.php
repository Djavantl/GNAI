@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Gerenciar Atributos do Tipo</h2>
            <p class="text-muted">Configurando campos para: <strong>{{ $type->name }}</strong></p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">Contexto de Uso</span>
            <span class="badge bg-purple fs-6">
                {{ $type->for_assistive_technology ? 'Tecnologia Assistiva' : 'Material Pedagógico' }}
            </span>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.type-attribute-assignments.update', $type->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Campo oculto para manter a referência do tipo --}}
            <input type="hidden" name="type_id" value="{{ $type->id }}">

            {{-- SEÇÃO 1: Atribuição de Atributos --}}
            <x-forms.section title="Campos do Formulário" />

            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold text-purple-dark px-2">
                    Marque os atributos técnicos que devem estar disponíveis para este tipo de recurso:
                </label>

                <div class="d-flex flex-wrap gap-2 p-4 border rounded bg-light mx-2">
                    @forelse($attributes as $attribute)
                        <div class="bg-white border rounded p-3 shadow-sm hover-shadow-transition d-flex align-items-center" style="min-width: 200px; flex: 1;">
                            <x-forms.checkbox
                                name="attribute_ids[]"
                                id="attr_{{ $attribute->id }}"
                                :value="$attribute->id"
                                :label="$attribute->label"
                                class="mb-0 fw-bold text-dark"
                                :checked="in_array($attribute->id, $assignedAttributeIds)"
                            />
                        </div>
                    @empty
                        <div class="col-12 text-center py-4">
                            <p class="text-muted italic">Nenhum atributo disponível para configuração.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SEÇÃO 2: Aviso de Impacto --}}
            <div class="col-12 px-4 mb-4">
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-0">
                    <i class="fas fa-exclamation-triangle fa-lg me-3 opacity-75 text-warning"></i>
                    <div class="small">
                        <strong>Atenção:</strong> Ao desmarcar um atributo, ele deixará de aparecer imediatamente nos formulários de cadastro e edição de todos os recursos vinculados a este tipo (<strong>{{ $type->name }}</strong>).
                    </div>
                </div>
            </div>

            {{-- Botões de Ação --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.type-attribute-assignments.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Alterações
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
