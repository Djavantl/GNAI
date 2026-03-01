@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'PEI' => route('specialized-educational-support.pei.show', $pei),
            'Editar Adaptação' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Adaptação Curricular</h2>
            <p class="text-muted">Atualize o planejamento pedagógico para a disciplina de <strong>{{ $peiDiscipline->discipline->name }}</strong>.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei) }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei-discipline.update', [$pei, $peiDiscipline]) }}" method="POST">
            @method('PUT')

            {{-- Inputs ocultos para manter a validação do Request, já que o usuário não pode editá-los --}}
            <input type="hidden" name="discipline_id" value="{{ $peiDiscipline->discipline_id }}">
            <input type="hidden" name="teacher_id" value="{{ $peiDiscipline->teacher_id }}">

            <x-forms.section title="Identificação (Somente Leitura)" />

            <div class="col-md-6">
                <x-forms.select
                    name="discipline_display"
                    label="Disciplina"
                    :options="[$peiDiscipline->discipline_id => $peiDiscipline->discipline->name]"
                    :selected="$peiDiscipline->discipline_id"
                    disabled
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="teacher_display"
                    label="Professor Responsável"
                    :options="[$peiDiscipline->teacher_id => $peiDiscipline->teacher->person->name]"
                    :selected="$peiDiscipline->teacher_id"
                    disabled
                />
            </div>

            <x-forms.section title="Planejamento Adaptado" />

            <div class="col-md-12">
                <x-forms.textarea
                    name="specific_objectives"
                    label="Objetivos Específicos"
                    rows="4"
                    required
                    :value="old('specific_objectives', $peiDiscipline->specific_objectives)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="content_programmatic"
                    label="Conteúdo Programático"
                    rows="4"
                    required
                    :value="old('content_programmatic', $peiDiscipline->content_programmatic)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="methodologies"
                    label="Metodologias e Estratégias"
                    rows="4"
                    required
                    :value="old('methodologies', $peiDiscipline->methodologies)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="evaluations"
                    label="Processo de Avaliação"
                    rows="4"
                    required
                    :value="old('evaluations', $peiDiscipline->evaluations)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Atualizar Adaptação
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection