@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Tipo de Recurso</h2>
            <p class="text-muted">Defina novas categorias para organizar seus equipamentos e materiais didáticos.</p>
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

            <x-forms.section title="Regras e Natureza do Recurso" />

            {{-- Coluna: Aplicação --}}
            <div class="col-md-6 mb-4"> {{-- Adicionado mb-4 aqui --}}
                <div class="p-3 border rounded bg-light h-100">
                    <label class="form-label fw-bold text-purple-dark mb-3 text-uppercase small">Este tipo se aplica a:</label>
                    <div class="d-flex flex-column gap-2">
                        <x-forms.checkbox
                            name="for_assistive_technology"
                            label="Tecnologias Assistivas"
                            :checked="old('for_assistive_technology')"
                        />
                        <x-forms.checkbox
                            name="for_educational_material"
                            label="Materiais Didáticos"
                            :checked="old('for_educational_material')"
                        />
                    </div>
                </div>
            </div>

            {{-- Coluna: Natureza Digital --}}
            <div class="col-md-6 mb-4"> {{-- Adicionado mb-4 aqui --}}
                <div class="p-3 border rounded h-100" style="background-color: #f0f7ff; border-color: #cfe2ff !important;">
                    <label class="form-label fw-bold text-primary mb-3 text-uppercase small italic">Natureza do Recurso:</label>
                    <x-forms.checkbox
                        name="is_digital"
                        label="Este recurso é digital?"
                        description="Marque para PDFs, Softwares ou Links."
                        :checked="old('is_digital')"
                    />
                </div>
            </div>

            <x-forms.section title="Status de Ativação" />

            <div class="col-md-12">
                <div class="p-3 border rounded border-success bg-light">
                    <x-forms.checkbox
                        name="is_active"
                        id="is_active"
                        label="Tipo de Recurso Ativo"
                        description="Se marcado, esta categoria aparecerá imediatamente nos formulários de cadastro."
                        :checked="old('is_active', true)"
                    />
                </div>
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.resource-types.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Tipo
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
