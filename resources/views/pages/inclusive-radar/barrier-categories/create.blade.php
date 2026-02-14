@extends('layouts.master')

@section('title', 'Cadastrar - Categoria de Barreira')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Categorias de Barreiras' => route('inclusive-radar.barrier-categories.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Nova Categoria de Barreira</h2>
            <p class="text-muted">Defina classificações para identificar obstáculos à acessibilidade.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.barrier-categories.store') }}" method="POST">
            @csrf

            {{-- SEÇÃO 1: Identificação --}}
            <x-forms.section title="Informações da Categoria" />

            <div class="col-md-12">
                <x-forms.input
                    name="name"
                    label="Nome da Categoria *"
                    required
                    :value="old('name')"
                    placeholder="Ex: Arquitetônica, Atitudinal, Comunicacional..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição Detalhada"
                    rows="4"
                    :value="old('description')"
                    placeholder="Descreva o que este tipo de barreira engloba..."
                />
            </div>

            {{-- SEÇÃO 2: Configurações --}}
            <x-forms.section title="Status e Visibilidade" />

            {{-- Status Ativo: Estilo Limpo TA (Sem divs extras) --}}
            <div class="col-md-12">
                <x-forms.checkbox
                    name="is_active"
                    id="is_active"
                    label="Ativar no Sistema"
                    description="Indica se esta categoria estará disponível para seleção no cadastro de novas barreiras"
                    :checked="old('is_active', true)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.barrier-categories.index') }}" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Voltar para Listagem
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar Categoria
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
