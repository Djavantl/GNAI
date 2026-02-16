@extends('layouts.master')

@section('title', 'Editar Avaliação do PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $pei->student->person->name => route('specialized-educational-support.students.show', $pei->student),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student),
            'PEI #' . $pei->id => route('specialized-educational-support.pei.show', $pei),
            'Avaliações' => route('specialized-educational-support.pei-evaluation.index', $pei),
            'Editar Avaliação' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Avaliação do PEI</h2>
            <p class="text-muted">
                Aluno: {{ $pei->student->person->name }} • 
                Disciplina: {{ $pei->discipline->name }} • 
                Semestre: {{ $pei_evaluation->semester->label }}
            </p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('specialized-educational-support.pei-evaluation.update', $pei_evaluation->id) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            <x-forms.section title="Conteúdo da Avaliação" />

            <div class="col-md-12">
                <x-forms.textarea
                    name="evaluation_instruments"
                    label="Instrumentos de Avaliação Utilizados *"
                    required
                    :value="old('evaluation_instruments', $pei_evaluation->evaluation_instruments)"
                />
            </div>

            <div class="col-md-12 mt-3">
                <x-forms.textarea
                    name="parecer"
                    label="Parecer Descritivo *"
                    required
                    :value="old('parecer', $pei_evaluation->parecer)"
                />
            </div>

            <div class="col-md-12 mt-3">
                <x-forms.textarea
                    name="successful_proposals"
                    label="Estratégias com Êxito *"
                    required
                    :value="old('successful_proposals', $pei_evaluation->successful_proposals)"
                />
            </div>

            <div class="col-md-12 mt-3">
                <x-forms.textarea
                    name="next_stage_goals"
                    label="Metas para a Próxima Etapa"
                    :value="old('next_stage_goals', $pei_evaluation->next_stage_goals)"
                />
            </div>

            <x-forms.section title="Informações Institucionais" />

            <x-show.info-item
                label="Tipo de Avaliação"
                :value="$pei_evaluation->evaluation_type->label()"
                column="col-md-3"
                isBox="true"
            />

            <x-show.info-item
                label="Data da Avaliação"
                :value="$pei_evaluation->evaluation_date->format('d/m/Y')"
                column="col-md-3"
                isBox="true"
            />

            <x-show.info-item
                label="Semestre"
                :value="$pei_evaluation->semester->label"
                column="col-md-3"
                isBox="true"
            />

            <x-show.info-item
                label="Profissional Responsável"
                :value="$pei_evaluation->professional->person->name"
                column="col-md-3"
                isBox="true"
            />

            <div class="col-12 d-flex justify-content-between border-t pt-4 px-4 pb-4 mt-4">
                <div>
                    <x-buttons.link-button
                        href="{{ route('specialized-educational-support.pei-evaluation.show', $pei_evaluation->id) }}"
                        variant="secondary"
                    >
                        Voltar para Detalhes
                    </x-buttons.link-button>
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.submit-button class="btn-action new submit px-5">
                        Atualizar Avaliação
                    </x-buttons.submit-button>
                </div>
            </div>

        </x-forms.form-card>

        {{-- Exclusão --}}
        <div class="mt-3 px-4 pb-4">
            <form
                action="{{ route('specialized-educational-support.pei-evaluation.destroy', $pei_evaluation->id) }}"
                method="POST"
                onsubmit="return confirm('Deseja realmente excluir esta avaliação do PEI?')"
            >
                @csrf
                @method('DELETE')

                <x-buttons.submit-button variant="danger">
                    Excluir Avaliação
                </x-buttons.submit-button>
            </form>
        </div>
    </div>
@endsection
