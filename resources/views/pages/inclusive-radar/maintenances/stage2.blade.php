@extends('layouts.master')

@section('title', 'Manutenção - Etapa 2')

@section('content')
    {{-- Lógica de bloqueio corrigida --}}
    @php
        // O uso de ?-> evita o erro se $stage2 for null
        $isLocked = (bool) ($stage2?->completed_at);

        // Busca a inspeção com segurança
        $inspection = null;
        if ($stage2) {
            $inspection = $stage2->inspection ?? $maintenance->maintainable->inspections()
                ->where('type', \App\Enums\InclusiveRadar\InspectionType::MAINTENANCE->value)
                // Só filtra por data se a etapa já tiver sido concluída
                ->when($stage2->completed_at, function($query) use ($stage2) {
                    return $query->whereDate('created_at', $stage2->completed_at->toDateString());
                })
                ->latest()
                ->first();
        }
    @endphp

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Manutenções' => route('inclusive-radar.maintenances.index'),
            'Manutenção ' . $maintenance->id => route('inclusive-radar.maintenances.show', $maintenance),
            'Etapa 1' => route('inclusive-radar.maintenances.stage1', $maintenance),
            'Etapa 2' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Etapa 2 – {{ $isLocked ? 'Manutenção Finalizada' : 'Finalização e Vistoria' }}</h2>
            <p class="text-muted mb-0">
                {{ $isLocked
                    ? 'O registro técnico desta manutenção foi concluído e está arquivado.'
                    : 'Confira os dados iniciais e registre a conclusão do serviço.' }}
            </p>
        </header>
        <div>
            <x-buttons.link-button :href="route('inclusive-radar.maintenances.show', $maintenance)" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mb-4">
        @include('pages.inclusive-radar.maintenances.partials.maintenance-stepper', ['maintenance' => $maintenance])
    </div>

    @if($isLocked)
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> <strong>Manutenção Concluída:</strong> Este processo foi encerrado. Os dados abaixo são apenas para consulta.
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card
            action="{{ $isLocked ? '#' : route('inclusive-radar.maintenances.saveStage2', $maintenance) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PATCH')

            <input type="hidden" name="maintenance_id" value="{{ $maintenance->id }}">
            <input type="hidden" name="step_number" value="2">
            <input type="hidden" name="inspection_type" value="{{ \App\Enums\InclusiveRadar\InspectionType::MAINTENANCE->value }}">

            {{-- SEÇÃO 1: LEITURA (Etapa 1) --}}
            <x-forms.section title="Informações da Manutenção (Leitura)" />

            <div class="col-md-6">
                <x-forms.input
                    name="view_estimated_cost"
                    label="Custo Estimado"
                    :value="'R$ ' . number_format($stage1?->estimated_cost ?? 0, 2, ',', '.')"
                    disabled
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="view_responsible"
                    label="Responsável pela Abertura"
                    :value="$stage1?->user?->name ?? 'Não identificado'"
                    disabled
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="view_damage"
                    label="Descrição do Dano"
                    rows="2"
                    :value="$stage1?->damage_description"
                    disabled
                />
            </div>

            <x-forms.section title="Finalização e Vistoria" />

            <div class="col-md-12">
                <x-forms.input
                    name="real_cost"
                    label="Custo Real da Manutenção"
                    type="text"
                    placeholder="0,00"
                    required
                    :value="old('real_cost', isset($stage2?->real_cost) ? number_format($stage2->real_cost, 2, ',', '.') : '')"
                    class="money"
                    :readonly="$isLocked"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="view_inspection_type"
                    label="Tipo de Inspeção"
                    :value="\App\Enums\InclusiveRadar\InspectionType::MAINTENANCE->label()"
                    disabled
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="inspection_date"
                    label="Data da Inspeção"
                    type="date"
                    required
                    :value="old('inspection_date', $stage2?->completed_at ? $stage2->completed_at->format('Y-m-d') : date('Y-m-d'))"
                    :readonly="$isLocked"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="state"
                    label="Estado de Conservação Pós-Manutenção"
                    required
                    :options="collect(\App\Enums\InclusiveRadar\ConservationState::cases())
                        ->filter(fn($state) => in_array($state, [
                            \App\Enums\InclusiveRadar\ConservationState::NEW,
                            \App\Enums\InclusiveRadar\ConservationState::GOOD,
                            \App\Enums\InclusiveRadar\ConservationState::REGULAR
                        ]))
                        ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                    :selected="old('state', $stage2?->inspection?->state->value ?? \App\Enums\InclusiveRadar\ConservationState::GOOD->value)"
                    :disabled="$isLocked"
                />
            </div>

            {{-- FOTINHAS NO MESMO LOCAL DO UPLOADER --}}
            <div class="col-md-6">
                @if(!$isLocked)
                    <x-forms.image-uploader
                        name="images[]"
                        label="Fotos de Evidência (Opcional)"
                        ariaLabel="Escolher fotos para upload"
                    />
                @else
                    <label class="form-label text-muted small text-uppercase fw-bold">Evidências Visuais</label>
                    <div class="p-2 border rounded bg-light d-flex flex-wrap gap-2" style="min-height: 45px;">
                        @if($inspection && $inspection->images->count() > 0)
                            @foreach($inspection->images as $image)
                                <a href="{{ asset('storage/' . $image->path) }}" target="_blank" class="d-block border rounded overflow-hidden">
                                    <img src="{{ asset('storage/' . $image->path) }}"
                                         style="height: 40px; width: 40px; object-fit: cover;"
                                         alt="Foto">
                                </a>
                            @endforeach
                        @else
                            <small class="text-muted m-auto">Sem fotos anexadas.</small>
                        @endif
                    </div>
                @endif
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="inspection_description"
                    label="Parecer Técnico / Descrição da Vistoria"
                    rows="3"
                    placeholder="Relate os serviços executados e o estado final do equipamento..."
                    required
                    :value="old('inspection_description', $stage2?->observation)"
                    :readonly="$isLocked"
                />
            </div>

            {{-- Ações Condicionais --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4">
                @if(!$isLocked)
                    <x-buttons.submit-button type="submit" name="finalize" value="0" variant="secondary">
                        <i class="fas fa-save me-1"></i> Salvar Rascunho
                    </x-buttons.submit-button>

                    <x-buttons.submit-button type="submit" name="finalize" value="1" class="btn-action new submit">
                        <i class="fas fa-check-circle me-1"></i> Concluir Manutenção
                    </x-buttons.submit-button>
                @else
                    <x-buttons.link-button :href="route('inclusive-radar.maintenances.show', $maintenance)" variant="secondary">
                        <i class="fas fa-eye me-1"></i> Visualizar Resumo
                    </x-buttons.link-button>
                @endif
            </div>
        </x-forms.form-card>
    </div>
@endsection
