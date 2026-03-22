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

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Editar Solicitação de Fila</h2>
            <p class="text-muted mb-0">Atualize as informações. Campos de identificação são bloqueados para manter a integridade do histórico.</p>
        </header>

        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('inclusive-radar.waitlists.show', $waitlist)" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.waitlists.update', $waitlist) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="waitlistable_id" value="{{ $waitlist->waitlistable_id }}">
            <input type="hidden" name="waitlistable_type" value="{{ $waitlist->waitlistable_type }}">

            <x-forms.section title="Recurso Solicitado" />

            <div class="col-md-12 mb-4 px-4">
                <div class="p-3 border rounded bg-light d-flex align-items-center gap-3 shadow-sm border-purple-light">
                    <div class="bg-purple-dark text-white p-3 rounded" style="background-color: #4c1d95;">
                        <i class="fas {{ $waitlist->waitlistable_type === 'assistive_technology' ? 'fa-microchip' : 'fa-book' }} fa-lg"></i>
                    </div>

                    <div>
                        <h5 class="mb-0 fw-bold">
                            @php
                                $resourceRoute = match($waitlist->waitlistable_type) {
                                    'assistive_technology'            => route('inclusive-radar.assistive-technologies.show', $waitlist->waitlistable_id),
                                    'accessible_educational_material' => route('inclusive-radar.accessible-educational-materials.show', $waitlist->waitlistable_id),
                                    default                           => '#',
                                };
                            @endphp

                            <a href="{{ $resourceRoute }}" class="text-purple-dark text-decoration-none hover-underline" target="_blank">
                                {{ $waitlist->waitlistable->name ?? ($waitlist->waitlistable->title ?? 'Recurso') }}
                                <i class="fas fa-external-link-alt ms-1" style="font-size: 0.75rem;"></i>
                            </a>
                        </h5>
                        <small class="text-muted text-uppercase fw-bold">Patrimônio: {{ $waitlist->waitlistable->asset_code ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>

            <x-forms.section title="Beneficiário e Responsável" />

            <div class="col-md-6">
                <x-forms.select
                    name="student_id"
                    label="Estudante (Beneficiário)"
                    :options="$students"
                    :selected="old('student_id', $waitlist->student_id)"
                    disabled
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    label="Profissional (Beneficiário)"
                    :options="$professionals"
                    :selected="old('professional_id', $waitlist->professional_id)"
                    disabled
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="user_id_display"
                    label="Usuário Responsável"
                    :value="$authUser->name"
                    disabled
                />
                <input type="hidden" name="user_id" value="{{ $authUser->id }}">
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="status_display"
                    label="Status Atual"
                    :value="$statusLabel"
                    disabled
                />
            </div>

            <x-forms.section title="Observações" />

            <div class="col-md-12">
                <x-forms.textarea
                    name="observation"
                    label="Notas e Prioridades"
                    rows="3"
                    :value="old('observation', $waitlist->observation)"
                    placeholder="Adicione informações relevantes sobre esta solicitação..."
                />
            </div>

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
@endsection
