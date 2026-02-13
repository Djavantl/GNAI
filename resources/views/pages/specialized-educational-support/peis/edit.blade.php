@extends('layouts.master')

@section('title', 'Editar PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'PEIs' => route('specialized-educational-support.pei.index', $student),
            'PEI #' . $pei->id => route('specialized-educational-support.pei.show', $pei),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar PEI: {{ $student->person->name }}</h2>
            <p class="text-muted">Atualize as informações básicas e o vínculo docente do plano.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei.update', $pei->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="student_id" value="{{ $student->id }}">

            <x-forms.section title="Vínculo Acadêmico" />

            <div class="col-md-6">
                <x-forms.select
                    name="course_id"
                    label="Curso *"
                    required
                    :options="$courses ?? []" 
                    :value="old('course_id', $pei->course_id)"
                    :selected="old('course_id', $pei->course_id)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="discipline_id"
                    label="Componente Curricular (Disciplina) *"
                    required
                    :options="$disciplines ?? []"
                    :value="old('discipline_id', $pei->discipline_id)"
                    :selected="old('discipline_id', $pei->discipline_id)"
                />
            </div>

            <div class="col-md-6 mt-3">
                <x-forms.input
                    name="teacher_name"
                    label="Nome do Docente Responsável *"
                    placeholder="Digite o nome do professor"
                    required
                    :value="old('teacher_name', $pei->teacher_name)"
                />
            </div>


            <x-show.info-item label="Semestre" 
                :value="$pei->semester->label" 
                column="col-md-6" 
                isBox="true" class="mt-3"/>

            <x-forms.section title="Contexto NAPNE Vinculado" />

            <div class="col-md-12">
                <div class="alert alert-light border d-flex align-items-center">
                    <i class="fas fa-link fa-2x me-3 text-muted"></i>
                    <div>
                        <strong>Contexto Utilizado:</strong><br>
                        Avaliação tipo <em>"{{ $pei->studentContext->evaluation_type }}"</em> realizada em {{ $pei->studentContext->created_at->format('d/m/Y') }}.
                    </div>
                    {{-- O contexto geralmente não muda em um PEI já iniciado para manter o histórico --}}
                    <input type="hidden" name="student_context_id" value="{{ $pei->student_context_id }}">
                </div>
            </div>

            <x-forms.section title="Status do Plano" />

            <div class="col-md-6">
                <x-forms.checkbox 
                    name="is_finished" 
                    label="Finalizar Plano (Impede novas edições de objetivos/metodologias)" 
                    :checked="old('is_finished', $pei->is_finished)" 
                />
            </div>

            <div class="col-12 d-flex justify-content-between border-t pt-4 px-4 pb-4 mt-4">
                <div>
                    <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei->id) }}" variant="secondary">
                        Voltar para Detalhes
                    </x-buttons.link-button>
                </div>

                <div class="d-flex gap-3">
                    <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                        Atualizar PEI
                    </x-buttons.submit-button>
                </div>
            </div>

        </x-forms.form-card>

        {{-- Formulário de exclusão separado seguindo o seu modelo --}}
        <div class="mt-3 px-4 pb-4">
            <form action="{{ route('specialized-educational-support.pei.destroy', $pei->id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: Isso excluirá o PEI e TODAS as metas/metodologias vinculadas. Deseja continuar?')">
                @csrf
                @method('DELETE')
                <x-buttons.submit-button type="submit" variant="danger">
                    Excluir PEI
                </x-buttons.submit-button>
            </form>
        </div>
    </div>
@endsection