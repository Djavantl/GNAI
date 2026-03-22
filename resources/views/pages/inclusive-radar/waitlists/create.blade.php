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
                Registre um beneficiário para um recurso atualmente indisponível. <br>
                <small class="text-purple-dark fw-bold">Selecione apenas um: aluno ou profissional.</small>
            </p>
        </div>
        <div>
            <x-buttons.link-button href="{{ route('inclusive-radar.waitlists.index') }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.waitlists.store') }}" method="POST">

            <x-forms.section title="Seleção do Recurso" />

            <div class="col-md-6">
                <x-forms.select
                    name="waitlistable_type"
                    id="waitlistable_type"
                    label="Tipo de Recurso"
                    required
                    :options="[
                        'assistive_technology' => 'Tecnologia Assistiva',
                        'accessible_educational_material' => 'Material Pedagógico'
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

            <x-forms.section title="Solicitante" />

            <div class="col-md-6">
                <x-forms.select
                    name="student_id"
                    id="student_id"
                    label="Aluno (Beneficiário)"
                    :options="$students"
                    :selected="old('student_id')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    id="professional_id"
                    label="Profissional (Beneficiário)"
                    :options="$professionals"
                    :selected="old('professional_id')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.input
                    name="user_id_display"
                    label="Usuário Responsável pelo Registro"
                    :value="$authUser->name"
                    disabled
                />
                <input type="hidden" name="user_id" value="{{ $authUser->id }}">
            </div>

            <x-forms.section title="Observações" />

            <div class="col-12">
                <x-forms.textarea
                    name="observation"
                    label="Observações Adicionais"
                    :value="old('observation')"
                    placeholder="Relate o motivo da urgência ou detalhes da solicitação..."
                    rows="3"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.waitlists.index') }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save me-1"></i> Cadastrar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    <script>
        window.waitlistData = {
            items: {
                'assistive_technology': @json($assistive_technologies ?? []),
                'accessible_educational_material': @json($educational_materials ?? [])
            },
            oldId: "{{ old('waitlistable_id') }}"
        };
    </script>

    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/waitlists.js')
    @endpush
@endsection
