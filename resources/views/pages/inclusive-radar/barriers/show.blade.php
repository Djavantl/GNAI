@extends('layouts.master')

@section('title', 'Detalhes da Barreira - ' . $barrier->name)

@section('content')
    {{-- Breadcrumb --}}
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Radar de Barreiras' => route('inclusive-radar.barriers.index'),
            'Detalhes da Barreira' => null
        ]" />
    </div>

    {{-- Cabeçalho com ações --}}
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Dossiê da Barreira</h2>
            <p class="text-muted mb-0">Histórico completo desde a identificação até a resolução.</p>
        </header>
        <div role="group">
            @php
                $nextStep = $barrier->nextStep();
                $isClosed = $barrier->isClosedOrNotApplicable();
            @endphp

            @if(!$isClosed && $nextStep)
                <x-buttons.link-button
                    :href="route('inclusive-radar.barriers.stage' . $nextStep, $barrier)"
                    variant="warning">
                    <i class="fas fa-edit"></i> Continuar Barreira (Etapa {{ $nextStep }})
                </x-buttons.link-button>
            @endif
            <x-buttons.link-button :href="route('inclusive-radar.barriers.index')" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Stepper de progresso --}}
    <div class="mt-3 mb-4">
        @include('pages.inclusive-radar.barriers.partials.barrier-stepper', ['barrier' => $barrier])
    </div>

    {{-- Conteúdo principal --}}
    <div class="mt-3">
        <main class="custom-table-card bg-white shadow-sm">

            {{-- ========== ETAPA 1 – IDENTIFICAÇÃO ========== --}}
            <x-forms.section title="1. Identificação da Barreira" />
            <div class="row g-3 px-4 pb-4">
                {{-- Linha 1: título e status atual --}}
                <x-show.info-item label="Título do Relato" column="col-md-8" isBox="true">
                    <strong>{{ $barrier->name }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Status Atual" column="col-md-4" isBox="true">
                    @if($barrier->status)
                        <span class="badge bg-{{ $barrier->status->color() }}-subtle text-{{ $barrier->status->color() }}-emphasis border px-3">
                            {{ strtoupper($barrier->status->label()) }}
                        </span>
                    @else
                        <span class="badge bg-light-subtle text-dark-emphasis border px-3">PENDENTE</span>
                    @endif
                </x-show.info-item>

                {{-- Linha 2: categoria, instituição, local --}}
                <x-show.info-item label="Categoria" column="col-md-4" isBox="true">
                    {{ $barrier->category->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Instituição (Campus)" column="col-md-4" isBox="true">
                    {{ $barrier->institution->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Local / Referência" column="col-md-4" isBox="true">
                    {{ $barrier->location->name ?? ($barrier->location_specific_details ?: '---') }}
                </x-show.info-item>

                {{-- Linha 3: prioridade, pessoa impactada, deficiências --}}
                <x-show.info-item label="Prioridade" column="col-md-3" isBox="true">
                    @if($barrier->priority)
                        <span class="badge bg-{{ $barrier->priority->color() }}-subtle text-{{ $barrier->priority->color() }}-emphasis border px-3">
                            {{ $barrier->priority->label() }}
                        </span>
                    @else
                        <span class="text-muted">---</span>
                    @endif
                </x-show.info-item>

                <x-show.info-item label="Pessoa Impactada" column="col-md-5" isBox="true">
                    @if($barrier->is_anonymous)
                        Contribuidor Anônimo
                    @elseif($barrier->affectedStudent)
                        Estudante: {{ $barrier->affectedStudent->person->name }}
                    @elseif($barrier->affectedProfessional)
                        Profissional: {{ $barrier->affectedProfessional->person->name }}
                    @elseif($barrier->affected_person_name)
                        {{ $barrier->affected_person_name }} ({{ $barrier->affected_person_role }})
                    @else
                        Não informado
                    @endif
                </x-show.info-item>

                <x-show.info-item label="Deficiências Relacionadas" column="col-md-4" isBox="true">
                    <div class="tag-container" role="list">
                        @forelse($barrier->deficiencies->sortBy('name') as $deficiency)
                            <x-show.tag color="light" role="listitem">
                                {{ $deficiency->name }}
                            </x-show.tag>
                        @empty
                            <span class="text-muted italic small">Nenhuma deficiência relacionada foi selecionada.</span>
                        @endforelse
                    </div>
                </x-show.info-item>

                {{-- Descrição do problema (ocupa linha inteira) --}}
                <x-show.info-item label="Descrição do Problema" column="col-md-12" isBox="true">
                    {{ $barrier->description ?: '---' }}
                </x-show.info-item>

                {{-- Datas e responsáveis --}}
                <x-show.info-item label="Data de Identificação" column="col-md-4" isBox="true">
                    {{ $barrier->identified_at ? $barrier->identified_at->format('d/m/Y') : '---' }}
                </x-show.info-item>

                <x-show.info-item label="Responsável pela Abertura" column="col-md-4" isBox="true">
                    {{ $barrier->starter->name ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Conclusão da Etapa 1" column="col-md-4" isBox="true">
                    {{-- Etapa 1 é concluída na criação --}}
                    {{ $barrier->created_at ? $barrier->created_at->format('d/m/Y H:i') : '---' }}
                </x-show.info-item>

                {{-- Vistoria Inicial --}}
                @if($barrier->initialInspection)
                    <div class="col-12 mt-3">
                        <label class="form-label text-muted small text-uppercase fw-bold mb-2">Vistoria Inicial</label>
                        <div class="history-timeline p-3 border rounded bg-light" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.barriers.inspection.show', [$barrier, $barrier->initialInspection]) }}'">
                            <x-forms.inspection-history-card :inspection="$barrier->initialInspection" />
                        </div>
                    </div>
                @endif

                {{-- Botão para visualizar/editar etapa 1 --}}
{{--                <div class="col-12 d-flex justify-content-end mt-2 pe-4">--}}
{{--                    <x-buttons.link-button--}}
{{--                        :href="route('inclusive-radar.barriers.stage1', $barrier)"--}}
{{--                        variant="info"--}}
{{--                        class="btn-sm">--}}
{{--                        <i class="fas fa-eye"></i> Ver Etapa 1--}}
{{--                    </x-buttons.link-button>--}}
{{--                </div>--}}
            </div>

            {{-- ========== ETAPA 2 – ANÁLISE TÉCNICA ========== --}}
            @if($barrier->step_number >= 2)
                <x-forms.section title="2. Análise Técnica" />
                <div class="row g-3 px-4 pb-4">
                    @if($barrier->status->value === 'not_applicable')
                        {{-- Caso encerrado como não aplicável --}}
                        <x-show.info-item label="Justificativa de Encerramento" column="col-md-12" isBox="true">
                            {{ $barrier->justificativa_encerramento ?? 'Não informado' }}
                        </x-show.info-item>
                    @else
                        {{-- Parecer do analista --}}
                        <x-show.info-item label="Parecer do Analista" column="col-md-12" isBox="true">
                            {{ $barrier->analyst_notes ?: 'Nenhuma observação.' }}
                        </x-show.info-item>

                        {{-- Prioridade definida na análise (se alterada) --}}
                        <x-show.info-item label="Prioridade (após análise)" column="col-md-4" isBox="true">
                            @if($barrier->priority)
                                <span class="badge bg-{{ $barrier->priority->color() }}-subtle text-{{ $barrier->priority->color() }}-emphasis border px-3">
                                    {{ $barrier->priority->label() }}
                                </span>
                            @else
                                <span class="text-muted">---</span>
                            @endif
                        </x-show.info-item>

                        <x-show.info-item label="Responsável pela Análise" column="col-md-4" isBox="true">
                            {{ $barrier->user->name ?? '---' }}
                        </x-show.info-item>

                        <x-show.info-item label="Conclusão da Etapa 2" column="col-md-4" isBox="true">
                            {{-- Usa a data da inspeção de análise como referência --}}
                            @if($barrier->stage2Inspection)
                                {{ $barrier->stage2Inspection->inspection_date ? $barrier->stage2Inspection->inspection_date->format('d/m/Y') : ($barrier->stage2Inspection->created_at ? $barrier->stage2Inspection->created_at->format('d/m/Y H:i') : '---') }}
                            @else
                                {{ $barrier->updated_at ? $barrier->updated_at->format('d/m/Y H:i') : '---' }}
                            @endif
                        </x-show.info-item>

                        {{-- Vistoria de Análise --}}
                        @if($barrier->stage2Inspection)
                            <div class="col-12 mt-3">
                                <label class="form-label text-muted small text-uppercase fw-bold mb-2">Vistoria de Análise</label>
                                <div class="history-timeline p-3 border rounded bg-light" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.barriers.inspection.show', [$barrier, $barrier->stage2Inspection]) }}'">
                                    <x-forms.inspection-history-card :inspection="$barrier->stage2Inspection" />
                                </div>
                            </div>
                        @endif
                    @endif

{{--                    <div class="col-12 d-flex justify-content-end mt-2 pe-4">--}}
{{--                        <x-buttons.link-button--}}
{{--                            :href="route('inclusive-radar.barriers.stage2', $barrier)"--}}
{{--                            variant="info"--}}
{{--                            class="btn-sm">--}}
{{--                            <i class="fas fa-eye"></i> Ver Etapa 2--}}
{{--                        </x-buttons.link-button>--}}
{{--                    </div>--}}
                </div>
            @else
                {{-- Placeholder para etapa não iniciada --}}
                <div class="px-4 pb-5 text-center">
                    <div class="p-5 border rounded bg-light border-dashed">
                        <i class="fas fa-search fa-3x mb-3 text-muted opacity-20"></i>
                        <p class="text-muted mb-0 italic">Aguardando a conclusão da Etapa 1 para iniciar a análise técnica.</p>
                    </div>
                </div>
            @endif

            {{-- ========== ETAPA 3 – PLANO DE TRATAMENTO ========== --}}
            @if($barrier->step_number >= 3)
                <x-forms.section title="3. Plano de Tratamento" />
                <div class="row g-3 px-4 pb-4">
                    <x-show.info-item label="Descrição do Plano de Ação" column="col-md-12" isBox="true">
                        {{ $barrier->action_plan_description ?: 'Não informado' }}
                    </x-show.info-item>

                    <x-show.info-item label="Data de Início da Intervenção" column="col-md-4" isBox="true">
                        {{ $barrier->intervention_start_date ? $barrier->intervention_start_date->format('d/m/Y') : '---' }}
                    </x-show.info-item>

                    <x-show.info-item label="Previsão de Conclusão" column="col-md-4" isBox="true">
                        {{ $barrier->estimated_completion_date ? $barrier->estimated_completion_date->format('d/m/Y') : '---' }}
                    </x-show.info-item>

                    <x-show.info-item label="Custo Estimado" column="col-md-4" isBox="true">
                        <strong>R$ {{ number_format($barrier->estimated_cost ?? 0, 2, ',', '.') }}</strong>
                    </x-show.info-item>

                    <x-show.info-item label="Responsável pelo Plano" column="col-md-6" isBox="true">
                        {{ $barrier->user->name ?? '---' }}
                    </x-show.info-item>

                    <x-show.info-item label="Conclusão da Etapa 3" column="col-md-6" isBox="true">
                        @if($barrier->stage3Inspection)
                            {{ $barrier->stage3Inspection->inspection_date ? $barrier->stage3Inspection->inspection_date->format('d/m/Y') : ($barrier->stage3Inspection->created_at ? $barrier->stage3Inspection->created_at->format('d/m/Y H:i') : '---') }}
                        @else
                            {{ $barrier->updated_at ? $barrier->updated_at->format('d/m/Y H:i') : '---' }}
                        @endif
                    </x-show.info-item>

                    {{-- Vistoria de Acompanhamento (Stage 3) --}}
                    @if($barrier->stage3Inspection)
                        <div class="col-12 mt-3">
                            <label class="form-label text-muted small text-uppercase fw-bold mb-2">Vistoria de Acompanhamento</label>
                            <div class="history-timeline p-3 border rounded bg-light" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.barriers.inspection.show', [$barrier, $barrier->stage3Inspection]) }}'">
                                <x-forms.inspection-history-card :inspection="$barrier->stage3Inspection" />
                            </div>
                        </div>
                    @endif

{{--                    <div class="col-12 d-flex justify-content-end mt-2 pe-4">--}}
{{--                        <x-buttons.link-button--}}
{{--                            :href="route('inclusive-radar.barriers.stage3', $barrier)"--}}
{{--                            variant="info"--}}
{{--                            class="btn-sm">--}}
{{--                            <i class="fas fa-eye"></i> Ver Etapa 3--}}
{{--                        </x-buttons.link-button>--}}
{{--                    </div>--}}
                </div>
            @elseif($barrier->step_number >= 2 && $barrier->status->value !== 'not_applicable')
                <div class="px-4 pb-5 text-center">
                    <div class="p-5 border rounded bg-light border-dashed">
                        <i class="fas fa-tasks fa-3x mb-3 text-muted opacity-20"></i>
                        <p class="text-muted mb-0 italic">Aguardando a conclusão da Etapa 2 para definir o plano de tratamento.</p>
                    </div>
                </div>
            @endif

            {{-- ========== ETAPA 4 – RESOLUÇÃO E VALIDAÇÃO ========== --}}
            @if($barrier->step_number >= 4)
                <x-forms.section title="4. Resolução e Validação" />
                <div class="row g-3 px-4 pb-4">
                    <x-show.info-item label="Resumo da Resolução" column="col-md-12" isBox="true">
                        {{ $barrier->resolution_summary ?: 'Não informado' }}
                    </x-show.info-item>

                    <x-show.info-item label="Validador" column="col-md-6" isBox="true">
                        {{ $barrier->validator->name ?? '---' }}
                    </x-show.info-item>

                    <x-show.info-item label="Data de Resolução" column="col-md-3" isBox="true">
                        {{ $barrier->resolution_date ? $barrier->resolution_date->format('d/m/Y') : '---' }}
                    </x-show.info-item>

                    <x-show.info-item label="Custo Real" column="col-md-3" isBox="true">
                        <strong class="text-success">R$ {{ number_format($barrier->actual_cost ?? 0, 2, ',', '.') }}</strong>
                    </x-show.info-item>

                    <x-show.info-item label="Nível de Efetividade" column="col-md-6" isBox="true">
                        @if($barrier->effectiveness_level)
                            <span class="badge bg-{{ $barrier->effectiveness_level->color() }}-subtle text-{{ $barrier->effectiveness_level->color() }}-emphasis border px-3">
                                {{ $barrier->effectiveness_level->label() }}
                            </span>
                        @else
                            <span class="text-muted">---</span>
                        @endif
                    </x-show.info-item>

                    @if($barrier->delay_justification)
                        <x-show.info-item label="Justificativa de Atraso" column="col-md-12" isBox="true">
                            {{ $barrier->delay_justification }}
                        </x-show.info-item>
                    @endif

                    @if($barrier->maintenance_instructions)
                        <x-show.info-item label="Instruções de Manutenção" column="col-md-12" isBox="true">
                            {{ $barrier->maintenance_instructions }}
                        </x-show.info-item>
                    @endif

                    <x-show.info-item label="Conclusão da Etapa 4" column="col-md-6" isBox="true">
                        @if($barrier->stage4Inspection)
                            {{ $barrier->stage4Inspection->inspection_date ? $barrier->stage4Inspection->inspection_date->format('d/m/Y') : ($barrier->stage4Inspection->created_at ? $barrier->stage4Inspection->created_at->format('d/m/Y H:i') : '---') }}
                        @else
                            {{ $barrier->updated_at ? $barrier->updated_at->format('d/m/Y H:i') : '---' }}
                        @endif
                    </x-show.info-item>

                    {{-- Vistoria de Resolução --}}
                    @if($barrier->stage4Inspection)
                        <div class="col-12 mt-3">
                            <label class="form-label text-muted small text-uppercase fw-bold mb-2">Vistoria de Resolução</label>
                            <div class="history-timeline p-3 border rounded bg-light" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.barriers.inspection.show', [$barrier, $barrier->stage4Inspection]) }}'">
                                <x-forms.inspection-history-card :inspection="$barrier->stage4Inspection" />
                            </div>
                        </div>
                    @endif

{{--                    <div class="col-12 d-flex justify-content-end mt-2 pe-4">--}}
{{--                        <x-buttons.link-button--}}
{{--                            :href="route('inclusive-radar.barriers.stage4', $barrier)"--}}
{{--                            variant="info"--}}
{{--                            class="btn-sm">--}}
{{--                            <i class="fas fa-eye"></i> Ver Etapa 4--}}
{{--                        </x-buttons.link-button>--}}
{{--                    </div>--}}
                </div>
            @elseif($barrier->step_number >= 3)
                <div class="px-4 pb-5 text-center">
                    <div class="p-5 border rounded bg-light border-dashed">
                        <i class="fas fa-check-circle fa-3x mb-3 text-muted opacity-20"></i>
                        <p class="text-muted mb-0 italic">Aguardando a conclusão da Etapa 3 para registrar a resolução.</p>
                    </div>
                </div>
            @endif

            {{-- ========== HISTÓRICO DE VISTORIAS ========== --}}
            <x-forms.section title="Histórico de Vistorias" />
            <div class="col-12 mb-4 px-4 pb-4">
                <div class="history-timeline p-4 border rounded bg-light" style="max-height: 450px; overflow-y: auto;" role="log">
                    @forelse($barrier->inspections->sortByDesc('inspection_date') as $inspection)
                        <div class="inspection-link d-block mb-3" style="cursor:pointer;" onclick="window.location='{{ route('inclusive-radar.barriers.inspection.show', [$barrier, $inspection]) }}'" role="link" tabindex="0">
                            <x-forms.inspection-history-card :inspection="$inspection" />
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted bg-white rounded border border-dashed">
                            <p class="fw-bold">Nenhuma vistoria registrada.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Rodapé com ações --}}
            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-barcode me-1"></i> Barreira #{{ $barrier->id }}
                    @if($isClosed)
                        {{-- Aqui pode incluir botão de PDF se existir rota --}}
                        {{--
                        <x-buttons.pdf-button :href="route('inclusive-radar.barriers.pdf', $barrier)" class="ms-2" />
                        --}}
                    @endif
                </div>

                <div class="d-flex gap-2">
                    @if(!$isClosed && $nextStep)
                        <x-buttons.link-button
                            :href="route('inclusive-radar.barriers.stage' . $nextStep, $barrier)"
                            variant="warning">
                            <i class="fas fa-tools"></i> Continuar (Etapa {{ $nextStep }})
                        </x-buttons.link-button>
                    @endif
                    <x-buttons.link-button :href="route('inclusive-radar.barriers.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar à Lista
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
