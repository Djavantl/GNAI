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

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Barreira de Acessibilidade</h2>
            <p class="text-muted">Atualizando informações de: <strong>{{ $barrier->name }}</strong></p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('inclusive-radar.barriers.update', $barrier->id) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            {{-- ========== LADO ESQUERDO: Dados Gerais ========== --}}
            <div class="col-lg-5 border-end">

                {{-- 1. Localização e Contexto --}}
                <x-forms.section title="1. Localização e Contexto" />

                <div class="col-md-12 mb-3 px-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-purple-dark italic">
                                Campus / Unidade *
                            </label>
                            <select name="institution_id"
                                    id="institution_select"
                                    required
                                    class="form-select custom-input shadow-sm">
                                <option value="">-- Selecione --</option>
                                @foreach($institutions as $inst)
                                    <option value="{{ $inst->id }}"
                                            data-lat="{{ $inst->latitude }}"
                                            data-lng="{{ $inst->longitude }}"
                                            data-zoom="{{ $inst->default_zoom ?? 16 }}"
                                        {{ old('institution_id', $barrier->institution_id) == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-purple-dark italic">
                                Local/Ponto de Referência
                            </label>
                            <select name="location_id"
                                    id="location_select"
                                    class="form-select custom-input shadow-sm">
                                <option value="">Selecione...</option>
                                @if($barrier->institution && $barrier->institution->locations)
                                    @foreach($barrier->institution->locations->where('is_active', true) as $location)
                                        <option value="{{ $location->id }}"
                                                data-lat="{{ $location->latitude }}"
                                                data-lng="{{ $location->longitude }}"
                                            {{ old('location_id', $barrier->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <div id="location_wrapper" class="col-md-12 mb-3 px-4 mt-3">
                    <x-forms.textarea
                        name="location_specific_details"
                        label="Complemento"
                        rows="3"
                        :value="old('location_specific_details', $barrier->location_specific_details)"
                    />
                </div>

                {{-- 2. Detalhes da Ocorrência --}}
                <x-forms.section title="2. Detalhes da Ocorrência" />

                <div class="px-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <x-forms.input
                                name="name"
                                label="Nome *"
                                required
                                :value="old('name', $barrier->name)"
                            />
                        </div>
                        <div class="col-md-4">
                            <x-forms.input
                                type="date"
                                name="identified_at"
                                label="Data *"
                                required
                                :value="old('identified_at', $barrier->identified_at?->format('Y-m-d') ?? now()->format('Y-m-d'))"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.select
                                name="priority"
                                label="Prioridade"
                                :options="collect(App\Enums\Priority::cases())->mapWithKeys(fn($c) => [$c->value => $c->label()])"
                                :selected="old('priority', $barrier->priority?->value)"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.select
                                name="barrier_category_id"
                                label="Categoria *"
                                required
                                :options="$categories->pluck('name', 'id')"
                                :selected="old('barrier_category_id', $barrier->barrier_category_id)"
                            />
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3 px-4 mt-3">
                    <x-forms.textarea
                        name="description"
                        label="Descrição *"
                        required
                        rows="3"
                        :value="old('description', $barrier->description)"
                    />
                </div>

                {{-- Pessoa Impactada --}}
                <div class="px-4">
                    <div class="bg-light p-3 rounded mb-4 border shadow-sm">
                        <label class="fw-bold text-purple-dark small uppercase mb-3 d-block">
                            Pessoa Impactada
                        </label>
                        <div class="d-flex flex-column gap-2">
                            <x-forms.checkbox
                                name="is_anonymous"
                                id="is_anonymous"
                                label="Relato Anônimo"
                                :checked="old('is_anonymous', $barrier->is_anonymous)"
                            />
                            <div id="wrapper_not_applicable">
                                <x-forms.checkbox
                                    name="not_applicable"
                                    id="not_applicable"
                                    label="Relato Geral"
                                    :checked="old('not_applicable', $barrier->not_applicable)"
                                />
                            </div>
                        </div>

                        <div id="identification_fields" class="mt-3">
                            <div id="person_selects"
                                 class="{{ old('not_applicable', $barrier->not_applicable) ? 'd-none' : '' }}">
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
                            <div id="manual_person_data"
                                 class="{{ old('not_applicable', $barrier->not_applicable) ? '' : 'd-none' }} mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.input
                                            name="affected_person_name"
                                            label="Nome"
                                            :value="old('affected_person_name', $barrier->affected_person_name)"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.input
                                            name="affected_person_role"
                                            label="Cargo"
                                            :value="old('affected_person_role', $barrier->affected_person_role)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deficiências Relacionadas --}}
                <div class="col-md-12 mb-4 px-4">
                    <label class="form-label fw-bold text-purple-dark">Deficiências *</label>
                    <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light max-h-40 overflow-y-auto custom-scrollbar">
                        @foreach($deficiencies as $def)
                            <x-forms.checkbox
                                name="deficiencies[]"
                                :value="$def->id"
                                :label="$def->name"
                                :checked="in_array($def->id, old('deficiencies', $barrier->deficiencies->pluck('id')->toArray()))"
                                class="mb-0"
                            />
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <x-forms.checkbox
                            name="is_active"
                            label="Ativo no Sistema"
                            description="Fica visível nos dashboards"
                            :checked="old('is_active', $barrier->is_active)"
                        />
                    </div>
                </div>
            </div>

            {{-- ========== LADO DIREITO: Mapa e Vistorias ========== --}}
            <div class="col-lg-7 bg-light px-0">
                <x-forms.section title="3. Localização no Mapa" />

                <div class="sticky-top" style="top:20px; z-index:1;">
                    <div class="mb-4">
                        <div class="mb-3 px-4">
                            <x-forms.checkbox
                                name="no_location"
                                id="no_location"
                                label="Sem localização física"
                                :checked="old('no_location', $barrier->latitude === null && $barrier->longitude === null)"
                            />
                        </div>

                        <x-forms.maps.barrier
                            :barrier="$barrier"
                            :institution="$barrier->institution"
                            height="450px"
                            label="Localização da Barreira"
                        />
                    </div>

                    <x-forms.section title="4. Histórico de Vistorias" />
                    <div class="px-4 mt-4">
                        <div class="custom-scrollbar" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                            @forelse($barrier->inspections()->with('images')->latest('inspection_date')->get() as $inspection)
                                {{--
                                    Chamamos o mesmo componente do Show.
                                    A lógica interna dele já vai identificar que é 'barrier'
                                    e mostrar os campos corretos.
                                --}}
                                <x-forms.inspection-history-card :inspection="$inspection" />
                            @empty
                                <div class="text-center py-4 text-muted bg-white rounded border border-dashed">
                                    <i class="fas fa-history fa-2x mb-2 opacity-20"></i>
                                    <p class="small fw-bold mb-0">Nenhuma vistoria registrada.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Nova Vistoria (layout idêntico ao TA) --}}
                    <div class="mt-3">
                        <x-forms.section title="5. Registrar Nova Vistoria" />
                        <div class="px-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.select
                                        name="status"
                                        label="Status *"
                                        :options="collect(\App\Enums\InclusiveRadar\BarrierStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])"
                                        :selected="old('status', $barrier->latestStatus()?->value)"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-forms.input
                                        type="date"
                                        name="inspection_date"
                                        label="Data da Vistoria *"
                                        :value="old('inspection_date', now()->format('Y-m-d'))"
                                    />
                                </div>
                                <div class="col-md-12">
                                    <x-forms.textarea
                                        name="inspection_description"
                                        label="Notas da Vistoria"
                                        rows="2"
                                        placeholder="Descreva o estado atual do local e ações realizadas..."
                                        :value="old('inspection_description')"
                                    />
                                </div>
                                <div class="col-md-12">
                                    {{-- COMPONENTE DE UPLOAD IGUAL AO TA --}}
                                    <x-forms.image-uploader
                                        name="images[]"
                                        label="Fotos de Evidência"
                                        :existingImages="old('images', [])"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER / BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4 bg-white">
                <x-buttons.link-button
                    href="{{ route('inclusive-radar.barriers.index') }}"
                    variant="secondary"
                >
                    <i class="fas fa-arrow-left"></i> Cancelar
                </x-buttons.link-button>
                <x-buttons.submit-button
                    type="submit"
                    class="btn-action new submit"
                >
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
    @endpush

    <script>
        window.institutionsData = @json($institutions);
        window.oldLocationId = "{{ old('location_id', $barrier->location_id) }}";
        window.barrierData = @json($barrier);
        window.initialInstitutionId = "{{ $barrier->institution_id }}";
    </script>
@endsection
