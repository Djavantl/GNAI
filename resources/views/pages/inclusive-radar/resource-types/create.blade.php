@extends('layouts.master')

@section('title', 'Cadastrar - Tipo de Recurso')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tipos de Recursos' => route('inclusive-radar.resource-types.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Tipo de Recurso</h2>
            <p class="text-muted">Defina novas categorias para organizar seus equipamentos e materiais didáticos.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.resource-types.store') }}" method="POST">
            @csrf

            <x-forms.section title="Identificação da Categoria" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Nome do Tipo *"
                    required
                    :value="old('name')"
                    placeholder="Ex: Teclados Adaptados, Softwares de Leitura, Próteses..."
                />
            </div>

            <x-forms.section title="Natureza e Visibilidade" />

            {{-- Natureza Digital: Estilo Limpo TA --}}
            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_digital"
                    label="Recurso Digital"
                    description="Marque para PDFs, Softwares ou Links (Uso Ilimitado)"
                    :checked="old('is_digital')"
                />
            </div>

            {{-- Status Ativo: Estilo Limpo TA --}}
            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    id="is_active"
                    label="Ativar no Sistema"
                    description="Fica disponível para uso nos formulários imediatamente"
                    :checked="old('is_active', true)"
                />
            </div>

            {{-- Aplicabilidade: Estilo Público-alvo TA (Box Cinza Horizontal) --}}
            <div class="col-md-12 mb-4 mt-4">
                <label class="form-label fw-bold text-purple-dark">Este tipo se aplica a: *</label>
                <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light">
                    <x-forms.checkbox
                        name="for_assistive_technology"
                        id="apply_ta"
                        label="Tecnologias Assistivas"
                        :checked="old('for_assistive_technology')"
                        class="mb-0"
                    />
                    <x-forms.checkbox
                        name="for_educational_material"
                        id="apply_material"
                        label="Materiais Pedagógicos Acessíveis"
                        :checked="old('for_educational_material')"
                        class="mb-0"
                    />
                </div>
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.resource-types.index') }}" variant="secondary">
                    Voltar para Listagem
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Tipo
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
