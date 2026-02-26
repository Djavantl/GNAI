@extends('layouts.master')

@section('title', 'Gerar PEI (Professor)')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'PEIs' => route('specialized-educational-support.pei.index', $student->id),
            'Gerar Novo (Professor)' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Gerar PEI: {{ $student->person->name }}</h2>
            <p class="text-muted">Como docente, selecione uma de suas disciplinas para iniciar o plano.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.pei.index', $student->id) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei.store') }}" method="POST">
            @csrf
            {{-- IDs essenciais enviados de forma oculta --}}
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            @if(isset($currentContext))
                <input type="hidden" name="student_context_id" value="{{ $currentContext->id }}">
            @endif

            <x-forms.section title="Vínculo Acadêmico" />

            <x-show.info-item 
                label="Seu Perfil Docente"
                column="col-md-6"
                isBox="true"
            >
                <strong>{{ auth()->user()->teacher->person->name }}</strong>
            </x-show.info-item>

            <x-show.info-item 
                label="Curso do Aluno"
                column="col-md-6"
                isBox="true"
            >
                {{ $course->name }}
            </x-show.info-item>

            <div class="col-md-6 mt-3">
                <x-forms.select
                    name="discipline_id"
                    label="Selecione sua Disciplina neste Curso"
                    required
                    :options="$disciplines"
                    :selected="old('discipline_id')"
                    aria-label="Selecione a disciplina"
                />
                <small class="text-muted">Apenas as disciplinas vinculadas ao seu perfil e ao curso do aluno são listadas.</small>
            </div>

            <x-show.info-item label="Semestre Atual" 
                :value="$semester->label" 
                column="col-md-6" 
                isBox="true" class="mt-3"/>

            <x-forms.section title="Contexto Pedagógico (NAPNE)" />

            <div class="col-md-12">
                @if(isset($currentContext) && $currentContext)
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                        <i class="fas fa-user-graduate fa-2x me-3"></i>
                        <div>
                            <strong>Análise do Estudante:</strong><br>
                            Este PEI será baseado no contexto avaliado em {{ $currentContext->created_at->format('d/m/Y') }}.<br>
                            <a href="{{ route('specialized-educational-support.student-context.pdf', $currentContext) }}" 
                                class="text-purple-dark fw-bold small" 
                                target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i> Consultar Avaliação de Especialista
                            </a>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning border-0 shadow-sm">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Aviso:</strong> O aluno não possui um contexto atual. Recomenda-se contatar o NAPNE antes de prosseguir.
                    </div>
                @endif
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pei.index', $student->id) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit" :disabled="!isset($currentContext)">
                    <i class="fas fa-save"></i> Iniciar Elaboração do PEI
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection