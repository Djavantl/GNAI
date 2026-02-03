@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Configurar Atributos por Tipo</h2>
            <p class="text-muted">Defina quais campos técnicos estarão disponíveis para cada categoria de recurso.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.type-attribute-assignments.store') }}" method="POST">

            {{-- SEÇÃO 1: Seleção do Alvo --}}
            <x-forms.section title="1. Alvo da Configuração" />

            <div class="col-md-12">
                <x-forms.select
                    name="type_id"
                    label="Para qual Tipo de Recurso deseja configurar atributos? *"
                    id="type_id"
                    required
                    :options="$types->mapWithKeys(fn($type) => [
                        $type->id => $type->name . ($type->for_assistive_technology ? ' (Assistiva)' : ' (Pedagógico)')
                    ])"
                    :selected="old('type_id')"
                />
            </div>

            {{-- SEÇÃO 2: Seleção de Atributos --}}
            <x-forms.section title="2. Seleção de Campos Disponíveis" />

            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold text-purple-dark px-2">Marque os atributos que devem aparecer no formulário:</label>

                <div class="d-flex flex-wrap gap-2 p-4 border rounded bg-light mx-2">
                    @forelse($attributes as $attribute)
                        <div class="bg-white border rounded p-3 shadow-sm hover-shadow-transition d-flex align-items-center" style="min-width: 200px; flex: 1;">
                            <x-forms.checkbox
                                name="attribute_ids[]"
                                id="attr_{{ $attribute->id }}"
                                :value="$attribute->id"
                                :label="$attribute->label"
                                class="mb-0 fw-bold text-dark"
                                :checked="is_array(old('attribute_ids')) && in_array($attribute->id, old('attribute_ids'))"
                            />
                        </div>
                    @empty
                        <div class="col-12 text-center py-4">
                            <p class="text-muted italic">Nenhum atributo cadastrado no sistema.</p>
                        </div>
                    @endforelse
                </div>

                @error('attribute_ids')
                <div class="text-danger small fw-bold mt-2 px-2">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror
            </div>

            {{-- Informativo de Gestão --}}
            <div class="col-12 px-4 mb-4">
                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-0">
                    <i class="fas fa-lightbulb fa-lg me-3 opacity-50 text-purple"></i>
                    <div class="small">
                        Os atributos marcados aqui serão exibidos na tela de cadastro/edição do material.
                        Para remover ou reordenar, acesse a <a href="{{ route('inclusive-radar.type-attribute-assignments.index') }}" class="fw-bold text-purple">Listagem de Vínculos</a>.
                    </div>
                </div>
            </div>

            {{-- Botões de Ação --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.type-attribute-assignments.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Configuração
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
