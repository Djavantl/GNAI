@extends('layouts.master')

@section('title', 'Realizar Empréstimo')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Empréstimos' => route('inclusive-radar.loans.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Registrar Novo Empréstimo</h2>
            <p class="text-muted">Vincule um recurso de acessibilidade a um estudante e defina os prazos de devolução.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <p class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-2"></i> Atenção: Existem erros no preenchimento.</p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.loans.store') }}" method="POST">
            @csrf

            {{-- SEÇÃO 1: Recurso Solicitado --}}
            <x-forms.section title="Seleção do Recurso" />

            <div class="col-md-6">
                <x-forms.select
                    name="loanable_type"
                    id="loanable_type"
                    label="Tipo de Recurso *"
                    required
                    :options="[
                        'App\\Models\\InclusiveRadar\\AssistiveTechnology' => 'Tecnologia Assistiva',
                        'App\\Models\\InclusiveRadar\\AccessibleEducationalMaterial' => 'Material Pedagógico'
                    ]"
                    :selected="old('loanable_type')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="loanable_id"
                    id="loanable_id"
                    label="Item Específico *"
                    required
                    :options="['' => 'Selecione o tipo primeiro']"
                />
            </div>

            {{-- SEÇÃO 2: Envolvidos --}}
            <x-forms.section title="Responsáveis e Beneficiário" />

            <div class="col-md-6">
                <x-forms.select
                    name="student_id"
                    label="Estudante Beneficiário *"
                    required
                    :options="$students->mapWithKeys(fn($s) => [$s->id => $s->person->name . ' (' . $s->registration . ')'])"
                    :selected="old('student_id')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    label="Profissional Responsável *"
                    required
                    :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person->name . ' - ' . $p->registration])"
                    :selected="old('professional_id')"
                />
            </div>

            {{-- SEÇÃO 3: Prazos e Condições --}}
            <x-forms.section title="Prazos e Observações" />

            <div class="col-md-6">
                <x-forms.input
                    name="loan_date"
                    label="Data e Hora do Empréstimo *"
                    type="datetime-local"
                    required
                    :value="old('loan_date', now()->format('Y-m-d\TH:i'))"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="due_date"
                    label="Previsão de Devolução *"
                    type="date"
                    required
                    :value="old('due_date')"
                />
                <small class="text-muted italic">Defina o prazo limite para a entrega do item.</small>
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="observation"
                    label="Observações / Estado do Item"
                    rows="3"
                    :value="old('observation')"
                    placeholder="Anote detalhes sobre o estado de conservação no momento da entrega..."
                />
            </div>

            {{-- Ações --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.loans.index') }}" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-handshake mr-2"></i> Confirmar Empréstimo
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>

    <script>
        window.loanData = {
            items: {
                'App\\Models\\InclusiveRadar\\AssistiveTechnology': @json($assistive_technologies ?? []),
                'App\\Models\\InclusiveRadar\\AccessibleEducationalMaterial': @json($educational_materials ?? [])
            },
            oldId: "{{ old('loanable_id') }}"
        };
    </script>
    @push('scripts')
        @vite('resources/js/pages/inclusive-radar/loans.js')
    @endpush
@endsection
