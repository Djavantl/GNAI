@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'PEI' => route('specialized-educational-support.pei.show', $pei),
            'Cadastrar Adaptação' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Adaptação Curricular</h2>
            <p class="text-muted">Defina os objetivos, conteúdos e metodologias adaptadas para este PEI.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei) }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei-discipline.store', $pei) }}" method="POST">
            
            <x-forms.section title="Identificação da Disciplina" />

            <div class="col-md-6">
                <x-forms.select
                    name="discipline_id"
                    label="Disciplina"
                    :options="$disciplines->pluck('name', 'id')->toArray()"
                    :value="old('discipline_id')"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="teacher_id"
                    label="Professor Responsável"
                    :options="$teachers->pluck('person.name', 'id')->toArray()"
                    :value="old('teacher_id')"
                    required
                />
            </div>

            <x-forms.section title="Planejamento Adaptado" />

            <div class="col-md-12">
                <x-forms.textarea
                    name="specific_objectives"
                    label="Objetivos Específicos"
                    rows="4"
                    required
                    :value="old('specific_objectives')"
                    placeholder="Descreva os objetivos de aprendizagem adaptados para o aluno..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="content_programmatic"
                    label="Conteúdo Programático"
                    rows="4"
                    required
                    :value="old('content_programmatic')"
                    placeholder="Liste os conteúdos que serão abordados nesta disciplina..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="methodologies"
                    label="Metodologias e Estratégias"
                    rows="4"
                    required
                    :value="old('methodologies')"
                    placeholder="Descreva como o conteúdo será ensinado (recursos, materiais, apoios)..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="evaluations"
                    label="Processo de Avaliação"
                    rows="4"
                    required
                    :value="old('evaluations')"
                    placeholder="Como a aprendizagem será avaliada nesta disciplina?"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i>Salvar Adaptação
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection