@extends('layouts.master')

@section('title', "Etapa 4 – Resolução da Barreira: {$barrier->name}")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Radar de Barreiras' => route('inclusive-radar.barriers.index'),
            'Etapa 4: Resolução' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Etapa 4 – Resolução e Validação</h2>
            <p class="text-muted">Finalize a barreira registrando a resolução, custos, efetividade e observações finais.</p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.barriers.index') }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Stepper --}}
    <div class="mb-4">
        @include('pages.inclusive-radar.barriers.partials.barrier-stepper', ['currentStep' => 4, 'barrier' => $barrier])
    </div>

    <div class="mt-3">
        <x-forms.form-card :action="route('inclusive-radar.barriers.saveStage4', $barrier)" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
                <div class="col-lg-5 border-end">
                    <x-forms.section title="1. Informações Registradas" />

                    <div class="px-4">
                        <x-forms.input name="name_display" label="Título do Relato" :value="$barrier->name" disabled />
                        <x-forms.input name="institution_display" label="Instituição" :value="$barrier->institution?->name ?? 'N/A'" disabled />
                        <x-forms.input name="category_display" label="Categoria" :value="$barrier->category?->name ?? 'Não categorizada'" disabled />
                        <x-forms.input name="affected_person_display" label="Pessoa Impactada"
                                       :value="$barrier->is_anonymous ? 'Contribuidor Anônimo' : ($barrier->affected_person_name ?? 'Não identificado')"
                                       disabled />
                        <x-forms.input name="affected_role_display" label="Papel / Cargo"
                                       :value="$barrier->is_anonymous ? 'N/A' : ($barrier->affected_person_role ?? 'N/A')" disabled />
                        <x-forms.textarea name="description_display" label="Descrição do Relatante"
                                          :value="$barrier->description" rows="4" disabled />
                    </div>

                    <x-forms.section title="2. Resolução e Parecer" />
                    <div class="px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input type="date" name="resolution_date" label="Data de Resolução"
                                               required :value="old('resolution_date', now()->format('Y-m-d'))" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input type="number" step="0.01" name="actual_cost" label="Custo Real (R$)"
                                               required :value="old('actual_cost')" placeholder="0,00" />
                            </div>

                            <div class="col-md-12">
                                <x-forms.textarea name="resolution_summary" label="Resumo da Resolução" rows="4"
                                                  required :value="old('resolution_summary')" />
                            </div>

                            <div class="col-md-12">
                                <x-forms.textarea name="delay_justification" label="Justificativa de Atraso (se houver)" rows="3"
                                                  :value="old('delay_justification')" />
                            </div>

                            <div class="col-md-6">
                                <x-forms.select
                                    name="effectiveness_level"
                                    label="Nível de Efetividade"
                                    required
                                    :options="collect(\App\Enums\InclusiveRadar\EffectivenessLevel::cases())
                                        ->mapWithKeys(fn($level) => [$level->value => $level->label()])"
                                    :selected="old('effectiveness_level')"
                                />
                            </div>

                            <div class="col-md-6">
                                <x-forms.select name="status_display" label="Status Atual" disabled
                                                :options="[\App\Enums\InclusiveRadar\BarrierStatus::RESOLVED->value => \App\Enums\InclusiveRadar\BarrierStatus::RESOLVED->label()]"
                                                :selected="\App\Enums\InclusiveRadar\BarrierStatus::RESOLVED->value" />
                                <input type="hidden" name="status" value="{{ \App\Enums\InclusiveRadar\BarrierStatus::RESOLVED->value }}">
                            </div>

                            <div class="col-md-12">
                                <x-forms.textarea name="maintenance_instructions" label="Instruções de Manutenção" rows="3"
                                                  :value="old('maintenance_instructions')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- LADO DIREITO: Mapa + Vistoria --}}
                <div class="col-lg-7 bg-light px-0">
                    <x-forms.section title="3. Localização no Mapa" />

                    <div class="sticky-top" style="top:20px; z-index:1;">
                        <div class="mb-4 px-4">
                            @if($barrier->no_location || (!$barrier->latitude && !$barrier->longitude))
                                <div class="text-center py-5 text-muted bg-white rounded border shadow-sm">
                                    <i class="fas fa-map-marked-alt fa-3x mb-3 opacity-20"></i>
                                    <p class="fw-bold">Barreira sem localização geográfica definida.</p>
                                </div>
                            @else
                                <x-show.maps.barrier :barrier="$barrier" :institution="$barrier->institution" height="300px"/>
                            @endif
                        </div>

                        <x-forms.section title="4. Vistoria de Resolução (Opcional)" />
                        <div class="px-4 mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.select name="inspection_type_display" label="Tipo de Inspeção"
                                                    :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())->mapWithKeys(fn($i) => [$i->value => $i->label()])"
                                                    :selected="\App\Enums\InclusiveRadar\InspectionType::PERIODIC->value"
                                                    disabled />
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
                                    <x-forms.image-uploader name="images[]" label="Fotos da Resolução"
                                                            :existingImages="old('images', [])" />
                                </div>

                                <div class="col-md-12">
                                    <x-forms.textarea name="inspection_description" label="Notas da Vistoria"
                                                      rows="2" placeholder="Observações técnicas encontradas durante a vistoria..."
                                                      :value="old('inspection_description')" />
                                </div>
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
                        <i class="fas fa-save me-2"></i> Salvar Resolução
                    </x-buttons.submit-button>
                </div>
        </x-forms.form-card>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa mapa
            if(typeof BarrierMap === 'function'){
                window.barrierMapInstance = new BarrierMap({
                    mapId: 'barrier-map',
                    lat: {{ $barrier->latitude ?? -15.8475 }},
                    lng: {{ $barrier->longitude ?? -47.9125 }},
                    zoom: 18,
                    isEditMode: true,
                    barrier: @json($barrier)
                });
            }
        });
    </script>
@endpush
