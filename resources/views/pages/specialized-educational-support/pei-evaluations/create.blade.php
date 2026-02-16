@extends('layouts.master')

@section('title', 'Nova Avaliação do PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $pei->student->person->name => route('specialized-educational-support.students.show', $pei->student),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student),
            'Avaliações do PEI' => route('specialized-educational-support.pei-evaluation.index', $pei),
            'Nova Avaliação' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Nova Avaliação do PEI</h2>
            <p class="text-muted">
                Aluno: {{ $pei->student->person->name }} • 
                Disciplina: {{ $pei->discipline->name }} • 
                Semestre do PEI: {{ $pei->semester->label }}
            </p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('specialized-educational-support.pei-evaluation.store', $pei) }}"
            method="POST"
        >
            @csrf

            <x-forms.section title="Informações da Avaliação" />

            <div class="col-md-12">
                <x-forms.textarea
                    name="evaluation_instruments"
                    label="Instrumentos de Avaliação Utilizados *"
                    placeholder="Ex: observação pedagógica, atividades adaptadas, avaliação oral..."
                    required
                />
            </div>

            <div class="col-md-12 mt-3">
                <x-forms.textarea
                    name="parecer"
                    label="Parecer Descritivo *"
                    placeholder="Descreva o desempenho do estudante considerando os objetivos do PEI..."
                    required
                />
            </div>

            <div class="col-md-12 mt-3">
                <x-forms.textarea
                    name="successful_proposals"
                    label="Estratégias com Êxito *"
                    placeholder="Quais metodologias e adaptações funcionaram melhor?"
                    required
                />
            </div>

            <div class="col-md-12 mt-3">
                <x-forms.textarea
                    name="next_stage_goals"
                    label="Metas para a Próxima Etapa"
                    placeholder="Defina orientações pedagógicas futuras..."
                />
            </div>

            <x-forms.section title="Informações Geradas Automaticamente" />

            <x-show.info-item
                label="Tipo de Avaliação"
                :value="$pei->is_finished ? 'Avaliação Final' : 'Avaliação de Progresso'"
                column="col-md-4"
                isBox="true"
            />

            <x-show.info-item
                label="Semestre Atual"
                :value="$semester->label ?? 'Definido automaticamente'"
                column="col-md-4"
                isBox="true"
            />

            <x-show.info-item
                label="Profissional Responsável"
                value="Registrado automaticamente no sistema"
                column="col-md-4"
                isBox="true"
            />

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button
                    href="{{ route('specialized-educational-support.pei-evaluation.index', $pei) }}"
                    variant="secondary"
                >
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Registrar Avaliação
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
