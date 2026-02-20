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

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Solicitação de Fila</h2>
            <p class="text-muted">Atualize as informações permitidas de acordo com o status atual. Apenas observações permanecem editáveis em qualquer situação.</p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">ID no Sistema</span>
            <span class="badge bg-purple fs-6">{{ $waitlist->id }}</span>
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

            {{-- SEÇÃO 1: Recurso (Bloqueado) --}}
            <x-forms.section title="Recurso Solicitado" />
            <div class="col-md-12 mb-4">
                <x-forms.input
                    name="resource_display"
                    label="Recurso"
                    :value="$waitlist->waitlistable->name ?? ($waitlist->waitlistable->title ?? 'Item não identificado')"
                    disabled
                />
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

            {{-- SEÇÃO 4: Observações --}}
            <x-forms.section title="Observações" />
            <div class="col-md-12">
                <x-forms.textarea
                    name="observation"
                    label="Observações"
                    rows="3"
                    :value="old('observation', $waitlist->observation ?? '')"
                    placeholder="Adicione observações ou notas sobre esta fila de espera..."
                />
            </div>

            {{-- BOTÕES --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.waitlists.index') }}" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Atualizar Solicitação
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/waitlists.js')
    @endpush
@endsection
