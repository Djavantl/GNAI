@extends('layouts.master')

@section('title', 'Relatar Barreira - Etapa 1')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Radar de Barreiras' => route('inclusive-radar.barriers.index'),
            'Etapa 1: Identificação' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Etapa 1 – Identificação da Barreira</h2>
            <p class="text-muted">Registre os detalhes iniciais e a localização da obstrução encontrada.</p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.barriers.index') }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    {{-- Stepper (Seguindo o padrão de Manutenções) --}}
    <div class="mb-4">
        @include('pages.inclusive-radar.barriers.partials.barrier-stepper', ['currentStep' => 1, 'barrier' => null])
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.barriers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- LADO ESQUERDO: Dados e Contexto (Col-5 igual ao do amigo) --}}
            <div class="col-lg-5 border-end">

                <x-forms.section title="1. Localização e Contexto" />
                <div class="col-md-12 mb-3 px-4">
                    <div class="row g-3">
                        {{-- Campus --}}
                        <div class="col-md-6">
                            <label for="institution_select" class="form-label fw-bold text-purple-dark italic">
                                Campus / Unidade
                            </label>
                            <select name="institution_id" id="institution_select" required class="form-select custom-input shadow-sm">
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

                        {{-- Local (Ponto de Referência) --}}
                        <div class="col-md-6">
                            <label for="location_id" class="form-label fw-bold text-purple-dark italic">
                                Local/Ponto de Referência
                            </label>
                            <select name="location_id" id="location_select" class="form-select custom-input shadow-sm">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Complemento --}}
                <div id="location_wrapper" class="{{ old('institution_id') ? '' : 'd-none' }} col-md-12 mb-3 px-4 mt-3">
                    <x-forms.textarea name="location_specific_details" label="Complemento/Detalhes do Local" rows="2" :value="old('location_specific_details')" />
                </div>

                <x-forms.section title="2. Detalhes da Ocorrência" />
                <div class="px-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <x-forms.input name="name" label="Título do Relato" required :value="old('name')" placeholder="Ex: Calçada irregular" />
                        </div>
                        <div class="col-md-4">
                            <x-forms.input type="date" name="identified_at" label="Data" required :value="old('identified_at', now()->format('Y-m-d'))" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.select name="priority" label="Prioridade" :options="collect(App\Enums\Priority::cases())->mapWithKeys(fn($case) => [$case->value => $case->label()])->toArray()" :selected="old('priority', 'medium')" />
                        </div>
                        <div class="col-md-6">
                            <x-forms.select name="barrier_category_id" label="Categoria" required :options="$categories->pluck('name', 'id')" :selected="old('barrier_category_id')" />
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3 px-4 mt-3">
                    <x-forms.textarea name="description" label="Descrição Detalhada" required rows="3" placeholder="Explique o problema encontrado..." :value="old('description')" />
                </div>

                {{-- Pessoa Impactada (Lógica completa do amigo) --}}
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
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <x-forms.select name="affected_student_id" label="Estudante" :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person?->name])" :selected="old('affected_student_id')" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.select name="affected_professional_id" label="Profissional" :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person?->name])" :selected="old('affected_professional_id')" />
                                    </div>
                                </div>
                            </div>
                            <div id="manual_person_data" class="{{ old('not_applicable') ? '' : 'd-none' }} mt-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <x-forms.input name="affected_person_name" label="Nome" :value="old('affected_person_name')" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.input name="affected_person_role" label="Cargo/Função" :value="old('affected_person_role')" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deficiências --}}
                <div class="col-md-12 mb-4 px-4">
                    <label class="form-label fw-bold text-purple-dark">Deficiências Relacionadas</label>
                    <div class="d-flex flex-wrap gap-4 p-3 border rounded bg-light max-h-40 overflow-y-auto custom-scrollbar">
                        @foreach($deficiencies as $def)
                            <x-forms.checkbox name="deficiencies[]" id="def_{{ $def->id }}" :value="$def->id" :label="$def->name" :checked="in_array($def->id, old('deficiencies', []))" class="mb-0" />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- LADO DIREITO: Mapa e Vistoria (Col-7 igual ao do amigo) --}}
            <div class="col-lg-7 bg-light px-0">
                <x-forms.section title="3. Localização no Mapa" id="map-section-title" />

                <div class="sticky-top" style="top:20px; z-index:1;">
                    <div class="mb-4">
                        <div class="mb-3 px-4">
                            <x-forms.checkbox name="no_location" id="no_location" label="Sem localização física (Barreira Atitudinal/Digital)" :checked="old('no_location')" />
                        </div>

                        @php
                            $selectedInstitution = old('institution_id') ? $institutions->firstWhere('id', old('institution_id')) : null;
                        @endphp

                        <x-forms.maps.barrier
                            :institution="$selectedInstitution"
                            height="450px"
                            label="Localização da Barreira"
                        />

                        {{-- Inputs de Lat/Lng escondidos ou para conferência --}}
                        <div class="row px-4 mt-2">
                            <div class="col-6">
                                <x-forms.input name="latitude" id="lat" label="Latitude" readonly :value="old('latitude')" />
                            </div>
                            <div class="col-6">
                                <x-forms.input name="longitude" id="lng" label="Longitude" readonly :value="old('longitude')" />
                            </div>
                        </div>
                    </div>

                    <x-forms.section title="4. Vistoria Inicial" />
                    <div class="px-4 pb-4">
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
                                    placeholder="Descreva o estado atual do local ou observações técnicas..."
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
                    <i class="fas fa-save me-2"></i> Salvar e Concluir Etapa 1
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

        // Lógica para alternar campos de pessoa impactada
        document.getElementById('not_applicable')?.addEventListener('change', function() {
            const personSelects = document.getElementById('person_selects');
            const manualData = document.getElementById('manual_person_data');
            if(this.checked) {
                personSelects.classList.add('d-none');
                manualData.classList.remove('d-none');
            } else {
                personSelects.classList.remove('d-none');
                manualData.classList.add('d-none');
            }
        });
    </script>
@endsection
