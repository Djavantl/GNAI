@extends('layouts.master')

@section('title', 'Relatar Barreira')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Barreiras' => route('inclusive-radar.barriers.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Relatar Barreira de Acessibilidade</h2>
            <p class="text-muted">Registre um ponto de obstrução ou dificuldade encontrada no campus.</p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.barriers.index') }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.barriers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- LADO ESQUERDO: Formulário --}}
            <div class="col-lg-5 border-end">

                {{-- 1. Localização e Contexto --}}
                <x-forms.section title="1. Localização e Contexto" />

                <div class="col-md-12 mb-3 px-4">
                    <div class="row">
                        {{-- Campus --}}
                        <div class="col-md-6">
                            <label for="institution_select"
                                   class="form-label fw-bold text-purple-dark italic">
                                Campus / Unidade <span class="text-danger"></span>
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
                                        {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Local --}}
                        <div class="col-md-6">
                            <label for="location_select"
                                   class="form-label fw-bold text-purple-dark italic">
                                Local/Ponto de Referência
                            </label>
                            <select name="location_id"
                                    id="location_select"
                                    class="form-select custom-input shadow-sm">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Complemento --}}
                <div id="location_wrapper"
                     class="{{ old('institution_id') ? '' : 'd-none' }} col-md-12 mb-3 px-4 mt-3">
                    <x-forms.textarea
                        name="location_specific_details"
                        label="Complemento"
                        rows="3"
                        :value="old('location_specific_details')"
                    />
                </div>

                {{-- 2. Detalhes da Ocorrência --}}
                <x-forms.section title="2. Detalhes da Ocorrência" />

                <div class="px-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <x-forms.input name="name" label="Título do Relato" required :value="old('name')" placeholder="Ex: Calçada irregular" />
                        </div>
                        <div class="col-md-4">
                            <x-forms.input type="date" name="identified_at" label="Data" required
                                           :value="old('identified_at', now()->format('Y-m-d'))" />
                        </div>

                        <div class="col-md-6">
                            <x-forms.select
                                name="priority"
                                label="Prioridade"
                                :options="collect(App\Enums\Priority::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray()"
                                :selected="old('priority', 'medium')"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.select
                                name="barrier_category_id"
                                label="Categoria"
                                required
                                :options="$categories->pluck('name', 'id')"
                                :selected="old('barrier_category_id')"
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
                        placeholder="Explique o problema encontrado..."
                        :value="old('description')"
                    />
                </div>

                {{-- Pessoa Impactada --}}
                <div class="px-4">
                    <div class="bg-light p-3 rounded mb-4 border shadow-sm">
                        <label class="fw-bold text-purple-dark small uppercase mb-3 d-block">Pessoa Impactada</label>
                        <div class="d-flex flex-column gap-2">
                            <x-forms.checkbox name="is_anonymous" id="is_anonymous" label="Relato Anônimo" :checked="old('is_anonymous')" />
                            <div id="wrapper_not_applicable">
                                <x-forms.checkbox name="not_applicable" id="not_applicable" label="Relato Geral" :checked="old('not_applicable')" />
                            </div>
                        </div>

                        <div id="identification_fields" class="mt-3">
                            <div id="person_selects" class="{{ old('not_applicable') ? 'd-none' : '' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.select
                                            name="affected_student_id"
                                            label="Estudante"
                                            :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person?->name])"
                                            :selected="old('affected_student_id')"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.select
                                            name="affected_professional_id"
                                            label="Profissional"
                                            :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person?->name])"
                                            :selected="old('affected_professional_id')"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div id="manual_person_data" class="{{ old('not_applicable') ? '' : 'd-none' }} mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-forms.input name="affected_person_name" label="Nome" :value="old('affected_person_name')" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.input name="affected_person_role" label="Cargo" :value="old('affected_person_role')" />
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
                                :checked="in_array($def->id, old('deficiencies', []))"
                                class="mb-0"
                            />
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="is_active" value="1">
            </div>

            {{-- LADO DIREITO: Mapa e Vistoria --}}
            <div class="col-lg-7 bg-light px-0">
                <x-forms.section title="3. Localização no Mapa" id="map-section-title" />

                <div class="sticky-top" style="top:20px; z-index:1;">
                    <div class="mb-4">
                        <div class="mb-3 px-4">
                            <x-forms.checkbox name="no_location" id="no_location" label="Sem localização física" :checked="old('no_location')" />
                        </div>

                        @php
                            $selectedInstitution = null;
                            $selectedInstitutionId = old('institution_id');
                            if ($selectedInstitutionId) {
                                $selectedInstitution = $institutions->firstWhere('id', $selectedInstitutionId);
                            }
                        @endphp

                        <x-forms.maps.barrier
                            :institution="$selectedInstitution"
                            height="450px"
                            label="Localização da Barreira"
                        />
                    </div>

                    {{-- ===== Vistoria Inicial (Padrão TA) ===== --}}
                    <div class="mt-3">
                        <x-forms.section title="4. Vistoria Inicial" />

                        <div class="px-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <x-forms.select
                                        name="status"
                                        id="status_select"
                                        label="Status Inicial"
                                        :options="collect(\App\Enums\InclusiveRadar\BarrierStatus::cases())->mapWithKeys(fn($s) => [$s->value => $s->label()])"
                                        :selected="old('status', 'identified')"
                                    />
                                </div>

                                <div class="col-md-6">
                                    {{-- !! COMPONENTE DE UPLOAD IGUAL AO TA !! --}}
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
                <x-buttons.link-button href="{{ route('inclusive-radar.barriers.index') }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar
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
        window.oldLocationId = "{{ old('location_id') }}";
    </script>
@endsection
