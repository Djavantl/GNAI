@extends('layouts.master')

@section('title', "Editar - $barrier->name")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Barreiras' => route('inclusive-radar.barriers.index'),
            $barrier->name => route('inclusive-radar.barriers.show', $barrier),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Barreira de Acessibilidade</h2>
            <p class="text-muted">Atualizando informações de: <strong>{{ $barrier->name }}</strong></p>
        </div>

        <div>
            <x-buttons.link-button :href="route('inclusive-radar.barriers.show', $barrier)" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.barriers.update', $barrier->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- LADO ESQUERDO: Formulário --}}
            <div class="col-lg-5 border-end">

                {{-- 1. Detalhes da Ocorrência (Incluindo Localização conforme seu Create) --}}
                <x-forms.section title="1. Detalhes da Ocorrência" />

                <div class="px-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <x-forms.input name="name" label="Título do Relato" required :value="old('name', $barrier->name)" />
                        </div>
                        <div class="col-md-4">
                            <x-forms.input type="date" name="identified_at" label="Data" required
                                           :value="old('identified_at', $barrier->identified_at?->format('Y-m-d'))" />
                        </div>

                        <div class="col-md-6">
                            <x-forms.select
                                name="priority"
                                label="Prioridade"
                                :options="collect(App\Enums\Priority::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray()"
                                :selected="old('priority', $barrier->priority?->value)"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.select
                                name="barrier_category_id"
                                label="Categoria"
                                required
                                :options="$categories->pluck('name', 'id')"
                                :selected="old('barrier_category_id', $barrier->barrier_category_id)"
                                extraAttributes="data-blocks-map-options"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-forms.select
                                name="institution_id"
                                id="institution_select"
                                label="Campus / Unidade"
                                required
                                :options="$institutions->pluck('name','id')"
                                :selected="old('institution_id', $barrier->institution_id)"
                                :resourceObjects="$institutions"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-forms.select
                                name="location_id"
                                id="location_select"
                                label="Local / Ponto de Referência"
                                :options="[]"
                                :selected="old('location_id', $barrier->location_id)"
                            />
                        </div>

                        {{-- Complemento --}}
                        <div id="location_wrapper" class="col-md-12 mb-3 mt-3">
                            <x-forms.textarea
                                name="location_specific_details"
                                label="Complemento"
                                rows="3"
                                placeholder="Descreva melhor onde a barreira está localizada..."
                                :value="old('location_specific_details', $barrier->location_specific_details)"
                            />
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3 px-4 mt-3">
                    <x-forms.textarea
                        name="description"
                        label="Descrição Detalhada"
                        required
                        rows="3"
                        :value="old('description', $barrier->description)"
                    />
                </div>

                {{-- Pessoa Impactada --}}
                <div class="px-4">
                    <div class="bg-light p-3 rounded mb-4 border shadow-sm">
                        <label class="fw-bold text-purple-dark small uppercase mb-3 d-block">Pessoa Impactada</label>
                        <div class="d-flex flex-column gap-2">
                            <x-forms.checkbox name="is_anonymous" id="is_anonymous" label="Relato Anônimo" :checked="old('is_anonymous', $barrier->is_anonymous)" />
                            <div id="wrapper_not_applicable">
                                <x-forms.checkbox name="not_applicable" id="not_applicable" label="Relato Geral" :checked="old('not_applicable', $barrier->not_applicable)" />
                            </div>
                        </div>

                        <div id="identification_fields" class="mt-3">
                            <div id="person_selects" class="{{ old('not_applicable', $barrier->not_applicable) ? 'd-none' : '' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.select
                                            name="affected_student_id"
                                            label="Estudante"
                                            :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person?->name])"
                                            :selected="old('affected_student_id', $barrier->affected_student_id)"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.select
                                            name="affected_professional_id"
                                            label="Profissional"
                                            :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person?->name])"
                                            :selected="old('affected_professional_id', $barrier->affected_professional_id)"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div id="manual_person_data" class="{{ old('not_applicable', $barrier->not_applicable) ? '' : 'd-none' }} mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.input name="affected_person_name" label="Nome" :value="old('affected_person_name', $barrier->affected_person_name)" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.input name="affected_person_role" label="Cargo" :value="old('affected_person_role', $barrier->affected_person_role)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deficiências Relacionadas --}}
                <div class="col-md-12 mb-4 px-4">
                    <label class="form-label fw-bold text-purple-dark">Deficiências Relacionadas</label>
                    <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light max-h-40 overflow-y-auto custom-scrollbar">
                        @foreach($deficiencies as $def)
                            <x-forms.checkbox
                                name="deficiencies[]"
                                id="def_{{ $def->id }}"
                                :value="$def->id"
                                :label="$def->name"
                                :checked="in_array($def->id, old('deficiencies', $barrier->deficiencies->pluck('id')->toArray()))"
                                class="mb-0"
                            />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- LADO DIREITO: Mapa e Histórico --}}
            <div class="col-lg-7 bg-light px-0">
                <x-forms.section title="3. Localização no Mapa" id="map-section-title" />

                <div class="sticky-top" style="top:20px; z-index:1;">
                    <div class="mb-4">
                        <div class="mb-3 px-4 d-flex justify-content-between align-items-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="btn-toggle-locations" checked style="cursor: pointer;">
                                <label class="form-check-label small text-muted fw-bold" for="btn-toggle-locations" style="cursor: pointer;">
                                    Exibir Locais (Cinza)
                                </label>
                            </div>
                        </div>

                        <div style="position: relative;">
                            <x-forms.maps.barrier
                                :barrier="$barrier"
                                :institution="$barrier->institution"
                                height="450px"
                                label="Localização da Barreira"
                            />

                            <div id="map-blocked-overlay" class="d-none"
                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #333; pointer-events: none; border-radius: 0.375rem;">
                                <span id="map-blocked-text" class="bg-white p-3 rounded shadow-sm"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Histórico de Vistorias --}}
                    <x-forms.section title="4. Histórico de Vistorias" />
                    <div class="px-4 mt-4">
                        <div class="custom-scrollbar" style="max-height:400px;overflow-y:auto">
                            @forelse($barrier->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                                <div class="inspection-link d-block mb-3"
                                     onclick="window.location='{{ route('inclusive-radar.barriers.inspection.show', [$barrier, $inspection]) }}'"
                                     style="cursor:pointer">
                                    <x-forms.inspection-history-card :inspection="$inspection" />
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted bg-white rounded border border-dashed">
                                    <i class="fas fa-history fa-2x mb-2 opacity-20"></i>
                                    <p class="small fw-bold mb-0">Nenhuma vistoria registrada.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="mt-3">
                        <x-forms.section title="4. Vistoria Periódica" />

                        <div class="px-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.select
                                        name="status"
                                        id="status_select"
                                        label="Status Atual"
                                        :options="collect(\App\Enums\InclusiveRadar\BarrierStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])"
                                        :selected="old('status', 'identified')"
                                    />
                                </div>

                                <div class="col-md-6">
                                    <x-forms.image-uploader
                                        name="images[]"
                                        label="Fotos da Barreira"
                                        :existingImages="old('images', [])"
                                    />
                                </div>

                                <div class="col-md-12">
                                    <x-forms.textarea
                                        name="inspection_description"
                                        id="inspection_description"
                                        label="Notas da Vistoria"
                                        rows="3"
                                        placeholder="Descreva o estado atual do local..."
                                        :value="old('inspection_description')"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER / BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4 bg-white">
                <x-buttons.link-button :href="route('inclusive-radar.barriers.show', $barrier)" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar Alterações
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            window.categoriesData = @json($categories->mapWithKeys(fn($cat) => [$cat->id => $cat->blocks_map]));
            window.institutionsData = @json($institutions);
            window.oldLocationId = "{{ old('location_id', $barrier->location_id) }}";
            window.barrierData = @json($barrier);
            window.initialInstitutionId = "{{ $barrier->institution_id }}";
        </script>
    @endpush
    @vite('resources/js/pages/inclusive-radar/barriers.js')
@endsection
