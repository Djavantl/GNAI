@extends('layouts.master')

@section('title', "$assignment->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Vínculos de Atributos' => route('inclusive-radar.type-attribute-assignments.index'),
            $assignment->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Detalhes do Tipo e Atributos</h2>
            <p class="text-muted">Visualize os campos técnicos vinculados a este tipo de recurso.</p>
        </div>
        <div>
            <x-buttons.link-button :href="route('inclusive-radar.type-attribute-assignments.edit', $assignment)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('inclusive-radar.type-attribute-assignments.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Informações do Tipo --}}
            <x-forms.section title="Informações do Tipo" />
            <div class="row g-3 mb-3">
                <x-show.info-item label="Nome do Tipo" column="col-md-6" isBox="true">
                    {{ $assignment->name }}
                </x-show.info-item>

                <x-show.info-item label="Utilizada em:" column="col-md-6" isBox="true">
                    {{ $assignment->for_assistive_technology ? 'Tecnologia Assistiva' : 'Materiais Pedagógicos Acessíveis' }}
                </x-show.info-item>
            </div>

            <div class="row g-3 mb-3">
                <x-show.info-item label="Digital" column="col-md-6" isBox="true">
                    {{ $assignment->is_digital ? 'Sim' : 'Não' }}
                </x-show.info-item>

                <x-show.info-item label="Ativo" column="col-md-6" isBox="true">
                    {{ $assignment->is_active ? 'Sim' : 'Não' }}
                </x-show.info-item>
            </div>

            <x-forms.section title="Campos Técnicos Vinculados" />
            <div class="row g-3 mb-4">
                <x-show.info-item label="Atributos Vinculados" column="col-md-12" isBox="true">
                    @if($assignment->attributes->isNotEmpty())
                        <div class="tag-container">
                            @foreach($assignment->attributes as $attribute)
                                <x-show.tag color="light">{{ $attribute->label }}</x-show.tag>
                            @endforeach
                        </div>
                    @else
                        ---
                    @endif
                </x-show.info-item>
            </div>

            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                {{-- ID do Sistema --}}
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $assignment->id }}
                </div>

                {{-- Ações --}}
                <div class="d-flex gap-3">
                    {{-- Excluir Todos os Vínculos --}}
                    <form action="{{ route('inclusive-radar.type-attribute-assignments.destroy', $assignment) }}"
                          method="POST"
                          onsubmit="return confirm('ATENÇÃO: Esta ação removerá todos os atributos vinculados. Confirmar?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    {{-- Voltar para Lista --}}
                    <x-buttons.link-button :href="route('inclusive-radar.type-attribute-assignments.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
