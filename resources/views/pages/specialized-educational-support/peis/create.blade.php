@extends('layouts.master')

@section('title', 'Gerar PEI')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'PEIs' => route('specialized-educational-support.pei.index', $student->id),
            'Gerar Novo' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Gerar Novo PEI: {{ $student->person->name }}</h2>
            <p class="text-muted">Inicie o Plano Educacional Individualizado vinculando-o a uma disciplina.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.pei.index', $student->id) }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei.store') }}" method="POST">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            
            <x-forms.section title="Dados Acadêmicos" />

            <input type="hidden" name="student_id" value="{{ $student->id }}">

            <x-show.info-item 
                label="Curso Vinculado"
                column="col-md-6"
                isBox="true"
            >
                <strong>{{ $course->name }}</strong>
            </x-show.info-item>

            <input type="hidden" name="course_id" value="{{ $course->id }}">

            <div class="col-md-6">
                <x-forms.select
                    name="discipline_id"
                    label="Disciplina "
                    required
                    :options="$disciplines"
                    :selected="old('discipline_id')"
                    aria-label="Selecione a disciplina do curso atual"
                />
            </div>

            <div class="col-md-6 mt-3">
                <x-forms.input
                    name="teacher_name"
                    label="Nome do Docente Responsável "
                    placeholder="Digite o nome do professor"
                    required
                    :value="old('teacher_name')"
                />
            </div>

            <x-show.info-item label="Semestre" 
                :value="$semester->label" 
                column="col-md-6" 
                isBox="true" class="mt-3"/>

            <x-forms.section title="Contexto NAPNE" />

            <div class="col-md-12">
                @if(isset($currentContext) && $currentContext)
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <strong>Contexto Atual Identificado:</strong><br>
                            Baseado na avaliação de {{ $currentContext->created_at->format('d/m/Y') }}.<br>
                            <a href="{{ route('specialized-educational-support.student-context.pdf', $currentContext) }}" 
                                class="text-purple-dark fw-bold small" 
                                target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> Ver Contexto
                            </a>
                        </div>
                        <input type="hidden" name="student_context_id" value="{{ $currentContext->id }}">
                    </div>
                @else
                    <div class="alert alert-danger border-0 shadow-sm">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Não foi encontrado um Contexto Atual ativo. O PEI requer um contexto prévio.
                    </div>
                @endif
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pei.index', $student->id) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                {{-- Removida a lógica ternária de dentro do componente para evitar erro de parse --}}
                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection