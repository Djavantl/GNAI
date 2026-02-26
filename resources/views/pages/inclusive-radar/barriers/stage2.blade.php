@extends('layouts.master')

@section('title', "Análise da Barreira – {$barrier->name}")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Radar de Barreiras' => route('inclusive-radar.barriers.index'),
            'Etapa 2: Análise Técnica' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Etapa 2 – Análise e Validação</h2>
            <p class="text-muted">Realize a análise técnica da barreira reportada e defina os próximos passos.</p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.barriers.index') }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Stepper --}}
    <div class="mb-4">
        @include('pages.inclusive-radar.barriers.partials.barrier-stepper', ['currentStep' => 2, 'barrier' => $barrier])
    </div>

    <div class="mt-3">
        {{-- Adicionado enctype para suporte a upload de imagens na vistoria --}}
        <x-forms.form-card :action="route('inclusive-radar.barriers.saveStage2', $barrier)" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- LADO ESQUERDO: Dados da Barreira (Col-5) --}}
            <div class="col-lg-5 border-end">
                <x-forms.section title="1. Informações Registradas" />

                <div class="px-4">
                    <div class="row g-3">
                        <div class="row g-3">
                            {{-- Instituição --}}
                            <div class="col-md-6">
                                <x-forms.input
                                    name="institution_display"
                                    label="Instituição (Campus)"
                                    :value="$barrier->institution?->name ?? 'N/A'"
                                    disabled
                                />
                            </div>

                            {{-- Local --}}
                            <div class="col-md-6">
                                <x-forms.input
                                    name="location_display"
                                    label="Ponto de Referência"
                                    :value="$barrier->location?->name ?? 'N/A'"
                                    disabled
                                />
                            </div>

                            <div class="col-md-12">
                                <x-forms.input
                                    name="name_display"
                                    label="Título do Relato"
                                    :value="$barrier->name"
                                    disabled
                                />
                            </div>

                            <div class="col-md-6">
                                <x-forms.input
                                    name="category_display"
                                    label="Categoria"
                                    :value="$barrier->category?->name ?? 'Não categorizada'"
                                    disabled
                                />
                            </div>

                            <div class="col-md-6">
                                <x-forms.select name="priority" label="Prioridade" :options="collect(App\Enums\Priority::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray()" :selected="old('priority', 'medium')" />
                            </div>

                            {{-- Nome do Impactado --}}
                            <div class="col-md-6">
                                <x-forms.input
                                    name="affected_person_display"
                                    label="Pessoa Impactada"
                                    :value="$barrier->is_anonymous ? 'Contribuidor Anônimo' : ($barrier->affected_person_name ?? ($barrier->affectedStudent?->person?->name ?? ($barrier->affectedProfessional?->person?->name ?? 'Não identificado')))"
                                    disabled
                                />
                            </div>

                            {{-- Papel/Cargo --}}
                            <div class="col-md-6">
                                <x-forms.input
                                    name="affected_role_display"
                                    label="Cargo / Papel"
                                    :value="$barrier->is_anonymous ? 'N/A' : ($barrier->affected_person_role ?? ($barrier->affected_student_id ? 'Estudante' : ($barrier->affected_professional_id ? 'Profissional/Servidor' : 'N/A')))"
                                    disabled
                                />
                            </div>
                        </div>

                        <input type="hidden" name="status" id="status_hidden" value="{{ \App\Enums\InclusiveRadar\BarrierStatus::UNDER_ANALYSIS->value }}">                        <input type="hidden" name="completed_at" value="{{ now()->format('Y-m-d H:i:s') }}">

                        {{-- Segue para a Descrição do Relatante --}}
                        <div class="col-md-12 mt-3">
                            <x-forms.textarea
                                name="description_display"
                                label="Descrição do Relatante"
                                :value="$barrier->description"
                                rows="4"
                                disabled
                            />
                            <input type="hidden" name="description" value="{{ $barrier->description }}">
                        </div>

                        {{-- Abaixo dos campos de Cargo / Papel --}}
                        <div class="col-md-12 mt-3 mb-4">
                            <label class="form-label fw-bold text-purple-dark italic">Deficiências Relacionadas (Público Afetado)</label>
                            <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light max-h-40 overflow-y-auto custom-scrollbar shadow-sm mb-2">
                                @foreach($deficiencies as $def)
                                    <x-forms.checkbox
                                        name="deficiencies[]"
                                        id="def_{{ $def->id }}"
                                        :value="$def->id"
                                        :label="$def->name"
                                        {{-- Verifica old() primeiro, depois o que já está salvo no banco --}}
                                        :checked="in_array($def->id, old('deficiencies', $barrier->deficiencies->pluck('id')->toArray()))"
                                        class="mb-0"
                                    />
                                @endforeach
                            </div>
                            <small class="text-muted">Marque ou desmarque as deficiências afetadas por esta barreira.</small>
                        </div>
                    </div>
                </div>
                <x-forms.section title="2. Parecer" />
                <div class="px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <x-forms.textarea
                                name="analyst_notes"
                                label="Notas da Análise Principal"
                                rows="3"
                                :value="old('analyst_notes')"
                                placeholder="Descreva as conclusões finais da análise técnica..."
                                required
                            />
                        </div>

                        <div class="col-md-12">
                            <div class="bg-white p-3 rounded border shadow-sm">
                                <label class="fw-bold text-purple-dark italic mb-2">Status da Validação</label>
                                <div class="d-flex flex-column gap-2">
                                    <x-forms.checkbox
                                        name="not_applicable"
                                        id="not_applicable"
                                        label="Desconsiderar esta barreira (Não se Aplica)"
                                        :checked="old('not_applicable')"
                                    />
                                </div>

                                <div id="justification_wrapper" class="mt-3 {{ old('not_applicable') ? '' : 'd-none' }}">
                                    <x-forms.textarea
                                        name="justificativa_encerramento"
                                        label="Justificativa de Encerramento"
                                        rows="2"
                                        :value="old('justificativa_encerramento')"
                                        placeholder="Explique por que esta barreira foi invalidada..."
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LADO DIREITO: Mapa, Vistoria e Parecer (Col-7) --}}
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
                            <x-show.maps.barrier
                                :barrier="$barrier"
                                :institution="$barrier->institution"
                                height="300px"
                            />
                        @endif
                    </div>

                    {{-- SEÇÃO 3: Vistoria Técnica --}}
                    <x-forms.section title="4. Vistoria de Análise (Opcional)" />
                    <div class="px-4 mb-4">
                        <div class="row g-2">
                            {{-- Tipo de Inspeção: Agora presente e seguindo o padrão do Stage 1 --}}
                            <div class="col-md-6">
                                <x-forms.select
                                    name="inspection_type_display"
                                    label="Tipo de Inspeção"
                                    :options="collect(\App\Enums\InclusiveRadar\InspectionType::cases())
                                        ->mapWithKeys(fn($item) => [$item->value => $item->label()])"
                                    :selected="\App\Enums\InclusiveRadar\InspectionType::PERIODIC->value"
                                    disabled
                                />
                                {{-- Input hidden caso precise enviar o valor mesmo estando disabled --}}
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
                                    id="status_display_input" {{-- Adicionado ID aqui --}}
                                name="status_display"
                                    label="Status da Barreira"
                                    :options="[
                                        \App\Enums\InclusiveRadar\BarrierStatus::UNDER_ANALYSIS->value => \App\Enums\InclusiveRadar\BarrierStatus::UNDER_ANALYSIS->label(),
                                        \App\Enums\InclusiveRadar\BarrierStatus::NOT_APPLICABLE->value => \App\Enums\InclusiveRadar\BarrierStatus::NOT_APPLICABLE->label()
                                    ]"
                                    :selected="\App\Enums\InclusiveRadar\BarrierStatus::UNDER_ANALYSIS->value"
                                    disabled
                                />
                            </div>

                            <div class="col-md-6">
                                <x-forms.image-uploader
                                    name="images[]"
                                    label="Novas Fotos da Análise"
                                    :existingImages="old('images', [])"
                                />
                            </div>

                            <div class="col-md-12">
                                <x-forms.textarea
                                    name="inspection_description"
                                    label="Notas da Vistoria"
                                    rows="2"
                                    placeholder="Observações técnicas específicas encontradas durante a visita de análise..."
                                    :value="old('inspection_description')"
                                />
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
                    <i class="fas fa-save me-2"></i> Salvar e Avançar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inicializa o Mapa (reutilizando sua classe)
            if (typeof BarrierMap === 'function') {
                window.barrierMapInstance = new BarrierMap({
                    mapId: 'barrier-map',
                    lat: {{ $barrier->latitude ?? -15.8475 }},
                    lng: {{ $barrier->longitude ?? -47.9125 }},
                    zoom: 18,
                    isEditMode: true,
                    barrier: @json($barrier)
                });
            }

            // 2. Lógica específica da Etapa 2 (Parecer e Status)
            const notApplicableCheck = document.getElementById('not_applicable');
            const justificationWrapper = document.getElementById('justification_wrapper');
            const statusHidden = document.querySelector('input[name="status"]');
            const statusDisplay = document.querySelector('select[name="status_display"]');

            if (notApplicableCheck) {
                notApplicableCheck.addEventListener('change', function() {
                    const isChecked = this.checked;

                    // Toggle da visualização da justificativa
                    if (justificationWrapper) {
                        justificationWrapper.classList.toggle('d-none', !isChecked);
                        // Opcional: tornar o campo obrigatório via JS se marcado
                        const textarea = justificationWrapper.querySelector('textarea');
                        if(textarea) textarea.required = isChecked;
                    }

                    // Troca os valores dos Enums dinamicamente
                    const statusUnderAnalysis = "{{ \App\Enums\InclusiveRadar\BarrierStatus::UNDER_ANALYSIS->value }}";
                    const statusNotApplicable = "{{ \App\Enums\InclusiveRadar\BarrierStatus::NOT_APPLICABLE->value }}";

                    if (statusHidden) statusHidden.value = isChecked ? statusNotApplicable : statusUnderAnalysis;
                    if (statusDisplay) statusDisplay.value = isChecked ? statusNotApplicable : statusUnderAnalysis;
                });
            }
        });
    </script>
@endpush
