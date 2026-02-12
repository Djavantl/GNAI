@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Pendências' => route('specialized-educational-support.pendencies.index'),
            $pendency->title => null
        ]" />
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Pendência — {{ $pendency->title }}</h2>
            <p class="text-muted">Detalhes completos da pendência.</p>
        </div>

        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.pendencies.edit', $pendency)" variant="warning">
                <i class="fas fa-edit me-1"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.pendencies.index')" variant="secondary">
                Voltar para Lista
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">

            <x-forms.section title="Dados da Pendência" />

            <x-show.info-item label="Título" column="col-md-8" isBox="true">
                <strong>{{ $pendency->title }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Profissional Responsável" column="col-md-4" isBox="true">
                {{ $pendency->assignedProfessional->person->name ?? ('#' . $pendency->assigned_to) }}
            </x-show.info-item>

            <x-show.info-item label="Prioridade" column="col-md-4" isBox="true">
                <span class="text-{{ $pendency->priority->color() }} fw-bold">
                    {{ $pendency->priority->label() }}
                </span>
            </x-show.info-item>

            <x-show.info-item label="Vencimento" column="col-md-4" isBox="true">
                {{ $pendency->due_date_formatted }}
            </x-show.info-item>

            <x-show.info-item label="Concluída" column="col-md-4" isBox="true">
                @if($pendency->is_completed)
                    <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Sim</span>
                @else
                    <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i>Não</span>
                @endif
            </x-show.info-item>

            <x-show.info-item label="Descrição" column="col-md-12" isBox="true">
                {!! nl2br(e($pendency->description ?? '—')) !!}
            </x-show.info-item>

            <x-forms.section title="Informações do Sistema" />

            <x-show.info-item label="Criado por" column="col-md-6" isBox="true">
                {{ $pendency->creator->professional->person->name ?? ('#' . $pendency->created_by) }}
            </x-show.info-item>

            <x-show.info-item label="Criado em" column="col-md-3" isBox="true">
                {{ $pendency->created_at_formatted }}
            </x-show.info-item>

            <x-show.info-item label="Última atualização" column="col-md-3" isBox="true">
                {{ $pendency->updated_at_formatted }}
            </x-show.info-item>

            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-list-alt me-1"></i> ID: #{{ $pendency->id }}
                </div>

                <div class="d-flex gap-3">
                    @if(! $pendency->is_completed)
                        <form action="{{ route('specialized-educational-support.pendencies.complete', $pendency) }}" method="POST" onsubmit="return confirm('Marcar como concluída?')">
                            @csrf
                            @method('PUT')
                            <x-buttons.submit-button variant="success">
                                <i class="fas fa-check me-1"></i> Marcar como Concluída
                            </x-buttons.submit-button>
                        </form>
                    @endif

                    <form action="{{ route('specialized-educational-support.pendencies.destroy', $pendency) }}" method="POST" onsubmit="return confirm('Deseja excluir esta pendência?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt me-1"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('specialized-educational-support.pendencies.edit', $pendency)" variant="warning">
                        <i class="fas fa-edit me-1"></i> Editar
                    </x-buttons.link-button>
                </div>
            </div>

        </div>
    </div>
@endsection
