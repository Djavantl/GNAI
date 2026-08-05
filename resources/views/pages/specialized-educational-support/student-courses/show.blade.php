@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $studentCourse->student->person->name => route('specialized-educational-support.students.show', $studentCourse->student),
            'Matrículas' => route('specialized-educational-support.student-courses.history', $studentCourse->student),
            $studentCourse->course->name => null
        ]" />
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h2 class="text-title">
                Matrícula — {{ $studentCourse->student->person->name }}
            </h2>
            <p class="text-muted">Detalhes completos da matrícula do aluno.</p>
        </div>

        <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
            @can('student-course.update')
            @if($studentCourse->is_current)
            <x-buttons.link-button
                :href="route('specialized-educational-support.student-courses.edit', $studentCourse)"
                variant="warning"
                aria-label="Editar matrícula">
                <i class="fas fa-edit me-1" aria-hidden="true"></i> Editar
            </x-buttons.link-button>
            @endif
            @endcan

            <x-buttons.link-button
                :href="route('specialized-educational-support.student-courses.history', $studentCourse->student)"
                variant="secondary"
                aria-label="Voltar para prontuário do aluno">
                <i class="fas fa-arrow-left "></i>  Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">

            <x-forms.section title="Dados da Matrícula" />

            <x-show.info-item label="Aluno" column="col-md-6" isBox="true">
                <strong>{{ $studentCourse->student->person->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Curso / Série" column="col-md-6" isBox="true">
                {{ $studentCourse->course->name }}
            </x-show.info-item>

            <x-show.info-item label="Ano Letivo" column="col-md-6" isBox="true">
                {{ $studentCourse->academic_year }}
            </x-show.info-item>

            <x-show.info-item label="Vigente" column="col-md-6" isBox="true">
                @if($studentCourse->is_current)
                    <span class="text-success" aria-label="Curso atual">SIM</span>
                @else
                    <span class="text-dark" aria-label="Não é curso atual">NÃO</span>
                @endif
            </x-show.info-item>

            <x-show.info-item label="Descrição do Curso" column="col-md-12" isBox="true">
                {!! nl2br(e($studentCourse->course->description ?? '—')) !!}
            </x-show.info-item>

            <x-forms.section title="Acompanhamento Acadêmico no Curso" />

            <x-show.info-textarea label="Situação da Frequência Escolar" column="col-md-12" isBox="true">
                {!! $studentCourse->school_attendance_status ?? 'Não informada.' !!}
            </x-show.info-textarea>

            @foreach([
                ['title' => 'Disciplinas com Reprovação', 'items' => $studentCourse->failedDisciplines, 'class' => 'bg-danger'],
                ['title' => 'Disciplinas com Risco de Insucesso Acadêmico', 'items' => $studentCourse->atRiskDisciplines, 'class' => 'bg-warning text-dark'],
            ] as $group)
                <x-show.info-item :label="$group['title']" column="col-md-6" isBox="true">
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($group['items'] as $discipline)
                            <span class="badge {{ $group['class'] }}">{{ $discipline->name }}</span>
                        @empty
                            <span class="text-muted">Nenhuma.</span>
                        @endforelse
                    </div>
                </x-show.info-item>
            @endforeach

            <x-forms.section title="Matriz Curricular" />

            <div class="col-12 p-4" aria-label="Lista de disciplinas do curso">
                <div class="d-flex flex-wrap gap-2">
                    @forelse($studentCourse->course->disciplines->sortBy('name') as $discipline)
                        <span class="badge rounded-pill bg-light text-dark border px-3 py-2" title="{{ $discipline->description }}">
                            {{ $discipline->name }}
                        </span>
                    @empty
                        <span class="text-muted">Nenhuma disciplina vinculada ao curso.</span>
                    @endforelse
                </div>
            </div>

            <x-forms.section title="Informações do Sistema" />

            <x-show.info-item label="Criado em" column="col-md-6" isBox="true">
                {{ $studentCourse->created_at->format('d/m/Y H:i') }}
            </x-show.info-item>

            <x-show.info-item label="Atualizado em" column="col-md-6" isBox="true">
                {{ $studentCourse->updated_at->format('d/m/Y H:i') }}
            </x-show.info-item>

            <div class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light no-print">
                <div class="d-flex flex-wrap gap-2">
                @can('student-course.delete')
                    <x-buttons.submit-button
                        type="button"
                        variant="danger"
                        data-bs-toggle="modal"
                        data-bs-target="#globalConfirmActionModal"
                        data-confirm-title="Excluir Matricula"
                        data-confirm-message="Deseja excluir esta matricula?"
                        data-confirm-action="{{ route('specialized-educational-support.student-courses.destroy', $studentCourse) }}"
                        data-confirm-method="DELETE"
                        data-confirm-submit-text="Confirmar Exclusao"
                        data-confirm-variant="danger"
                        aria-label="Excluir matrícula">
                            <i class="fas fa-trash-alt me-1" aria-hidden="true"></i> Excluir
                        </x-buttons.submit-button>
                @endcan
                </div>
            </div>

        </div>
    </div>
@endsection
