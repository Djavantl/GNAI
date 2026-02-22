@extends('layouts.master')

@section('title', 'Detalhes da Manutenção - ' . $maintenance->maintainable->name)

@section('content')
    {{-- Cabeçalho --}}
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Manutenções' => route('inclusive-radar.maintenances.index'),
            'Detalhes da Manutenção' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Dossiê de Manutenção</h2>
            <p class="text-muted mb-0">Histórico completo desde a abertura do chamado até a vistoria de encerramento.</p>
        </header>
        <div role="group">
            @if($maintenance->status->value !== \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED->value)
                <x-buttons.link-button
                    :href="route('inclusive-radar.maintenances.stage' . ($maintenance->stages->max('step_number') ?? 1), $maintenance)"
                    variant="warning">
                    <i class="fas fa-edit"></i> Continuar Manutenção
                </x-buttons.link-button>
            @endif
            <x-buttons.link-button :href="route('inclusive-radar.maintenances.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <main class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Identificação do Recurso --}}
            <x-forms.section title="Identificação do Recurso" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Equipamento" column="col-md-8" isBox="true">
                    <strong>{{ $maintenance->maintainable->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Status Atual da Manutenção" column="col-md-4" isBox="true">
                    <span class="badge bg-{{ $maintenance->status->value === 'completed' ? 'success' : 'warning' }}-subtle text-{{ $maintenance->status->value === 'completed' ? 'success' : 'warning' }}-emphasis border px-3">
                        {{ strtoupper($maintenance->status->value) }}
                    </span>
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Etapa 1 - Diagnóstico e Abertura --}}
            @php $stage1 = $maintenance->stages->where('step_number', 1)->first(); @endphp
            <x-forms.section title="Etapa 1: Diagnóstico Inicial" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Descrição do Dano / Problema" column="col-md-12" isBox="true">
                    {{ $stage1->damage_description ?? 'Não informado' }}
                </x-show.info-item>

                <x-show.info-item label="Custo Estimado" column="col-md-4" isBox="true">
                    <strong>R$ {{ number_format($stage1->estimated_cost ?? 0, 2, ',', '.') }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Responsável pela Abertura" column="col-md-4" isBox="true">
                    {{ $stage1->starter->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Data de Conclusão Etapa 1" column="col-md-4" isBox="true">
                    {{ $stage1->completed_at ? $stage1->completed_at->format('d/m/Y H:i') : 'Pendente' }}
                </x-show.info-item>

                <x-show.info-item label="Observações de Abertura" column="col-md-12" isBox="true">
                    {{ $stage1->observation ?? 'Nenhuma observação adicional.' }}
                </x-show.info-item>

                {{-- Botão Etapa 1 --}}
                <div class="col-12 d-flex justify-content-end mt-2 pe-4">
                    <x-buttons.link-button
                        :href="route('inclusive-radar.maintenances.stage1', $maintenance)"
                        variant="info"
                        class="btn-sm"
                    >
                        <i class="fas fa-eye"></i> Ver Etapa 1
                    </x-buttons.link-button>
                </div>
            </div>

            {{-- SEÇÃO 3: Etapa 2 - Execução e Vistoria Final --}}
            @php $stage2 = $maintenance->stages->where('step_number', 2)->first(); @endphp
            @if($stage2)
                <x-forms.section title="Etapa 2: Finalização e Vistoria Técnica" />
                <div class="row g-3 px-4 pb-4">
                    <x-show.info-item label="Parecer Técnico Final" column="col-md-12" isBox="true">
                        {{ $stage2->observation ?? 'Não informado' }}
                    </x-show.info-item>

                    <x-show.info-item label="Custo Real do Serviço" column="col-md-4" isBox="true">
                        <strong class="text-success">R$ {{ number_format($stage2->real_cost ?? 0, 2, ',', '.') }}</strong>
                    </x-show.info-item>

                    <x-show.info-item label="Responsável pelo Encerramento" column="col-md-4" isBox="true">
                        {{ $stage2->user->name ?? '---' }}
                    </x-show.info-item>

                    <x-show.info-item label="Data da Manutenção" column="col-md-4" isBox="true">
                        {{ $stage2->completed_at ? $stage2->completed_at->format('d/m/Y') : '---' }}
                    </x-show.info-item>

                    {{-- SEÇÃO 4: Histórico de Vistoria (Estilo TA) --}}
                    @php
                        $inspection = $maintenance->maintainable->inspections()
                            ->where('type', \App\Enums\InclusiveRadar\InspectionType::MAINTENANCE->value)
                            ->whereDate('created_at', $stage2->completed_at?->toDateString())
                            ->latest()
                            ->first();

                        $resourceRoutePrefix = str_contains(get_class($maintenance->maintainable), 'AssistiveTechnology')
                            ? 'assistive-technologies'
                            : 'accessible-educational-materials';
                    @endphp

                    @if($inspection)
                        <div class="col-12 mt-4">
                            <label class="form-label text-muted small text-uppercase fw-bold mb-3">Vistoria Técnica Vinculada</label>
                            <div class="history-timeline p-4 border rounded bg-light" role="log">
                                <div class="inspection-link d-block" style="cursor:pointer;"
                                     onclick="window.location='{{ route("inclusive-radar.$resourceRoutePrefix.inspection.show", [$maintenance->maintainable, $inspection]) }}'"
                                     role="link" tabindex="0">
                                    <x-forms.inspection-history-card :inspection="$inspection" />
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Botão Etapa 2 --}}
                    <div class="col-12 d-flex justify-content-end mt-4 pe-4">
                        <x-buttons.link-button
                            :href="route('inclusive-radar.maintenances.stage2', $maintenance)"
                            variant="info"
                            class="btn-sm"
                        >
                            <i class="fas fa-eye"></i> Ver Etapa 2
                        </x-buttons.link-button>
                    </div>
                </div>
            @else
                <div class="px-4 pb-5 text-center">
                    <div class="p-5 border rounded bg-light border-dashed">
                        <i class="fas fa-tools fa-3x mb-3 text-muted opacity-20"></i>
                        <p class="text-muted mb-0 italic">Aguardando a conclusão da Etapa 2 para exibir os dados da vistoria final.</p>
                    </div>
                </div>
            @endif

            {{-- Rodapé de Ações --}}
            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-barcode me-1"></i> Chamado: #{{ $maintenance->id }}
                    @if($maintenance->status->value === \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED->value)
                        <x-buttons.pdf-button :href="route('inclusive-radar.maintenances.pdf', $maintenance)" class="ms-2" />
                    @endif
                </div>

                <div class="d-flex gap-2">
                    @if($maintenance->status->value !== \App\Enums\InclusiveRadar\MaintenanceStatus::COMPLETED->value)
                        <x-buttons.link-button
                            :href="route('inclusive-radar.maintenances.stage' . ($maintenance->stages->whereNotNull('completed_at')->count() + 1), $maintenance)"
                            variant="warning">
                            <i class="fas fa-tools"></i> Continuar
                        </x-buttons.link-button>
                    @endif
                    <x-buttons.link-button :href="route('inclusive-radar.maintenances.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar à Lista
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
