@extends('layouts.master')

@section('title', 'Registrar Fila de Espera')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Fila de Espera' => route('inclusive-radar.waitlists.index'),
        'Cadastrar' => null
    ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Nova Solicitação de Fila</h2>
            <p class="text-muted">
                Registre um estudante ou profissional na fila para um recurso indisponível. <br>
                <strong>Importante:</strong> selecione apenas um: aluno ou profissional.
            </p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.waitlists.index') }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <p class="font-weight-bold mb-1">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Atenção: Existem erros no preenchimento.
            </p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.waitlists.store') }}" method="POST">
            @csrf

            {{-- SEÇÃO 1: Recurso --}}
            <x-forms.section title="Seleção do Recurso" />

            <div class="col-md-6">
                <x-forms.select
                    name="waitlistable_type"
                    id="waitlistable_type"
                    label="Tipo de Recurso"
                    required
                    :options="[
                    'App\\Models\\InclusiveRadar\\AssistiveTechnology' => 'Tecnologia Assistiva',
                    'App\\Models\\InclusiveRadar\\AccessibleEducationalMaterial' => 'Material Pedagógico'
                ]"
                    :selected="old('waitlistable_type')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="waitlistable_id"
                    id="waitlistable_id"
                    label="Item Específico"
                    required
                    :options="['' => 'Selecione o tipo primeiro']"
                />
            </div>

            {{-- SEÇÃO 2: Solicitante --}}
            <x-forms.section title="Solicitante" />

            <div class="col-md-6">
                <x-forms.select
                    name="student_id"
                    id="student_id"
                    label="Aluno"
                    :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person->name . ' (' . $s->registration . ')'])"
                    :selected="old('student_id')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    id="professional_id"
                    label="Profissional"
                    :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person->name])"
                    :selected="old('professional_id')"
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

            {{-- SEÇÃO 3: Observações --}}
            <x-forms.section title="Observações" />

            <div class="col-12">
                <x-forms.textarea
                    name="observation"
                    id="observation"
                    label="Observações"
                    :value="old('observation')"
                    placeholder="Digite alguma observação sobre a solicitação (opcional)"
                    rows="3"
                />
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.waitlists.index') }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save me-1"></i> Cadastrar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    {{-- Script para popular itens dinamicamente --}}
    <script>
        window.waitlistData = {
            items: {
                'App\\Models\\InclusiveRadar\\AssistiveTechnology': @json($assistive_technologies ?? []),
                'App\\Models\\InclusiveRadar\\AccessibleEducationalMaterial': @json($educational_materials ?? [])
            },
            oldId: "{{ old('waitlistable_id') }}"
        };
    </script>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/waitlists.js')
    @endpush
@endsection
