@extends('layouts.master')

@section('title', "$barrierCategory->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Categorias de Barreiras' => route('inclusive-radar.barrier-categories.index'),
            $barrierCategory->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes da Categoria de Barreira</h2>
            <p class="text-muted">
                Visualize as informações cadastrais da categoria:
                <strong>{{ $barrierCategory->name }}</strong>
            </p>
        </div>

        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID do Registro</span>
            <span class="badge bg-purple fs-6">#{{ $barrierCategory->id }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Informações da Categoria --}}
            <x-forms.section title="Informações da Categoria" />

            <div class="row g-3 mb-4">

                <x-show.info-item label="Nome da Categoria" column="col-md-12" isBox="true">
                    <strong>{{ $barrierCategory->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição Detalhada" column="col-md-12" isBox="true">
                    {{ $barrierCategory->description ?: '— Não informada —' }}
                </x-show.info-item>

            </div>

            {{-- SEÇÃO 2: Status e Visibilidade --}}
            <x-forms.section title="Status e Visibilidade" />

            <div class="row g-3 mb-4">

                <x-show.info-item label="Ativo no Sistema" column="col-md-6" isBox="true">
                    {{ $barrierCategory->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>

            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i>
                    ID do Sistema: #{{ $barrierCategory->id }}
                </div>

                <div class="d-flex gap-3">

                    <form action="{{ route('inclusive-radar.barrier-categories.destroy', $barrierCategory) }}"
                          method="POST"
                          onsubmit="return confirm('ATENÇÃO: Esta ação excluirá esta categoria de barreira. Confirmar?')">
                        @csrf
                        @method('DELETE')

                        <x-buttons.submit-button variant="danger">
                            Excluir Categoria
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button
                        :href="route('inclusive-radar.barrier-categories.edit', $barrierCategory)"
                        variant="warning">
                        Editar Categoria
                    </x-buttons.link-button>

                    <x-buttons.link-button
                        :href="route('inclusive-radar.barrier-categories.index')"
                        variant="secondary">
                        Voltar para Lista
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
