@extends('layouts.master')

@section('title', "Editar - Fila de Espera #$waitlist->id")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Filas de Espera' => route('inclusive-radar.waitlists.index'),
            $waitlist->id => route('inclusive-radar.waitlists.show', $waitlist),
            'Editar' => null
        ]" />
    </div>

    {{-- Header Sincronizado com Loan --}}
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Editar Solicitação de Fila</h2>
            <p class="text-muted mb-0">Atualize as informações da solicitação. Note que campos de identificação são bloqueados para integridade.</p>
        </header>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('inclusive-radar.waitlists.show', $waitlist)" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <p class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Atenção: Existem erros no preenchimento.</p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.waitlists.update', $waitlist) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Campos ocultos para integridade --}}
            <input type="hidden" name="waitlistable_id" value="{{ $waitlist->waitlistable_id }}">
            <input type="hidden" name="waitlistable_type" value="{{ $waitlist->waitlistable_type }}">

            {{-- SEÇÃO 1: Recurso (Bloqueado) - Agora idêntica ao Loan --}}
            {{-- SEÇÃO 1: Recurso Solicitado --}}
            <x-forms.section title="Recurso Solicitado" />

            <div class="col-md-12 mb-4 px-4">
                <div class="p-3 border rounded bg-light d-flex align-items-center gap-3">
                    <div class="bg-purple-dark text-white p-3 rounded shadow-sm" style="background-color: #4c1d95;">
                        <i class="fas {{ $waitlist->waitlistable_type === 'assistive_technology' ? 'fa-microchip' : 'fa-book' }} fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="mb-0 fw-bold">
                            @php
                                $type = $waitlist->waitlistable_type;
                                $id = $waitlist->waitlistable_id;

                                // Lógica de rota usando os aliases do MorphMap
                                $resourceRoute = match($type) {
                                    'assistive_technology'            => route('inclusive-radar.assistive-technologies.show', $id),
                                    'accessible_educational_material' => route('inclusive-radar.accessible-educational-materials.show', $id),
                                    default                           => '#',
                                };
                            @endphp

                            <a href="{{ $resourceRoute }}"
                               class="text-purple-dark text-decoration-none hover-underline"
                               target="_blank"
                               aria-label="Ver detalhes do recurso: {{ $waitlist->waitlistable->name }} (abre em nova aba)">
                                {{ $waitlist->waitlistable->name ?? ($waitlist->waitlistable->title ?? 'Recurso') }}
                                <i class="fas fa-external-link-alt ms-1" aria-hidden="true" style="font-size: 0.70rem;"></i>
                            </a>
                        </h5>
                        <small class="text-muted text-uppercase">Patrimônio: {{ $waitlist->waitlistable->asset_code ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>

            {{-- SEÇÃO 2: Beneficiário e Usuário --}}
            <x-forms.section title="Beneficiário e Usuário Responsável" />

            <div class="col-md-6">
                <x-forms.select
                    name="student_id"
                    id="student_id"
                    label="Estudante (Beneficiário)"
                    :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person->name . ' (' . $s->registration . ')'])"
                    :selected="old('student_id', $waitlist->student_id)"
                    disabled
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    id="professional_id"
                    label="Profissional (Beneficiário)"
                    :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person->name])"
                    :selected="old('professional_id', $waitlist->professional_id)"
                    disabled
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="user_id_display"
                    label="Usuário Autenticado (Responsável)"
                    :value="$authUser->name"
                    disabled
                />
                <input type="hidden" name="user_id" value="{{ $authUser->id }}">
            </div>

            {{-- SEÇÃO 3: Status --}}
            <div class="col-md-6">
                <x-forms.input
                    name="status_display"
                    label="Status Atual"
                    :value="\App\Enums\InclusiveRadar\WaitlistStatus::tryFrom($waitlist->status)?->label() ?? 'Status desconhecido'"
                    disabled
                />
            </div>

            {{-- SEÇÃO 3: Observações --}}
            <x-forms.section title="Observações" />
            <div class="col-md-12">
                <x-forms.textarea
                    name="observation"
                    label="Observações Adicionais"
                    rows="3"
                    :value="old('observation', $waitlist->observation ?? '')"
                    placeholder="Adicione notas sobre a solicitação ou prioridade..."
                />
            </div>

            {{-- BOTÕES Sincronizados com Loan --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button :href="route('inclusive-radar.waitlists.show', $waitlist)" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save me-1"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/waitlists.js')
    @endpush
@endsection
