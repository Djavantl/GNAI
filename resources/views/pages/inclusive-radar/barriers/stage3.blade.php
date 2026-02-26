@extends('layouts.master')

@section('title', "Etapa 3 – Em Tratamento: {$barrier->name}")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Radar de Barreiras' => route('inclusive-radar.barriers.index'),
            'Etapa 3: Em Tratamento' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Etapa 3 – Em Tratamento</h2>
            <p class="text-muted">Preencha os dados do plano de ação e acompanhe a execução da correção.</p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.barriers.index') }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Stepper --}}
    <div class="mb-4">
        @include('pages.inclusive-radar.barriers.partials.barrier-stepper', ['currentStep' => 3, 'barrier' => $barrier])
    </div>

    <div class="mt-3">
        <x-forms.form-card :action="route('inclusive-radar.barriers.saveStage3', $barrier)" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- LADO ESQUERDO: Dados da Barreira e Plano de Ação --}}
            <div class="col-lg-5 border-end">
                <x-forms.section title="1. Informações Registradas" />

                <div class="px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-forms.input name="inst_display" label="Instituição" :value="$barrier->institution?->name ?? 'N/A'" disabled />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input name="loc_display" label="Ponto de Referência" :value="$barrier->location?->name ?? 'N/A'" disabled />
                        </div>
                        <div class="col-md-12">
                            <x-forms.input name="name_display" label="Título do Relato" :value="$barrier->name" disabled />
                        </div>
                    </div>
                </div>

                <x-forms.section title="2. Plano de Ação" />
                <div class="px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <x-forms.textarea
                                name="action_plan_description"
                                label="Descrição Detalhada da Execução"
                                rows="4"
                                placeholder="Descreva as ações que estão sendo tomadas..."
                                :value="old('action_plan_description')"
                                required
                            />
                        </div>

                        <div class="col-md-6">
                            <x-forms.input type="date" name="intervention_start_date" label="Data de Início" :value="old('intervention_start_date', now()->format('Y-m-d'))" required />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input type="date" name="estimated_completion_date" label="Previsão de Conclusão" :value="old('estimated_completion_date')" required />
                        </div>
                        <div class="col-md-12">
                            <x-forms.input type="number" step="0.01" name="estimated_cost" label="Custo Estimado (R$)" :value="old('estimated_cost')" placeholder="0,00" required />
                        </div>
                    </div>
                </div>
            </div>

            {{-- LADO DIREITO: Mapa e Vistoria --}}
            <div class="col-lg-7 bg-light px-0">
                <x-forms.section title="3. Localização no Mapa" />
                <div class="px-4 mb-4">
                    @if(!$barrier->latitude || !$barrier->longitude)
                        <div class="alert alert-warning py-3">
                            <i class="fas fa-exclamation-triangle me-2"></i> Barreira sem coordenadas geográficas.
                        </div>
                    @else
                        <x-show.maps.barrier :barrier="$barrier" :institution="$barrier->institution" height="350px" />
                    @endif
                </div>

                <x-forms.section title="4. Vistoria de Acompanhamento (Opcional)" />
                <div class="px-4 mb-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <x-forms.select
                                name="inspection_type_display"
                                label="Tipo de Inspeção"
                                :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                                :selected="\App\Enums\InclusiveRadar\InspectionType::PERIODIC->value"
                                disabled
                            />
                            {{-- Mantemos o hidden apenas para campos que podem variar por etapa e não estão no merge fixo do Request --}}
                            <input type="hidden" name="inspection_type" value="{{ \App\Enums\InclusiveRadar\InspectionType::PERIODIC->value }}">
                        </div>

                        <div class="col-md-6">
                            <x-forms.input
                                name="inspection_date"
                                label="Data da Inspeção"
                                type="date"
                                :value="old('inspection_date', date('Y-m-d'))"
                            />
                        </div>

                        {{-- Status da Barreira: Mantém a coerência visual --}}
                        <div class="col-md-6">
                            <x-forms.select
                                name="status_display"
                                label="Status Atual"
                                :options="[
                                    \App\Enums\InclusiveRadar\BarrierStatus::IN_PROGRESS->value => \App\Enums\InclusiveRadar\BarrierStatus::IN_PROGRESS->label()
                                ]"
                                :selected="\App\Enums\InclusiveRadar\BarrierStatus::IN_PROGRESS->value"
                                disabled
                            />
                        </div>

                        <div class="col-md-6">
                            <x-forms.image-uploader name="images[]" label="Fotos do Início da Execução" :existingImages="old('images', [])" />
                        </div>

                        <div class="col-md-12">
                            <x-forms.textarea
                                name="inspection_description"
                                label="Observações da Vistoria"
                                rows="2"
                                placeholder="Notas sobre o estado inicial da intervenção..."
                                :value="old('inspection_description')"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4 bg-white">
                <x-buttons.link-button href="{{ route('inclusive-radar.barriers.index') }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save me-2"></i> Salvar e Iniciar Tratamento
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection
