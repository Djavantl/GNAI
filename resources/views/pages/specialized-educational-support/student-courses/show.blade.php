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

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">
                Matrícula — {{ $studentCourse->student->person->name }}
            </h2>
            <p class="text-muted">Detalhes completos da matrícula do aluno.</p>
        </div>

        <div class="d-flex gap-2">
            <x-buttons.link-button
                :href="route('specialized-educational-support.student-courses.edit', $studentCourse)"
                variant="warning"
                aria-label="Editar matrícula">
                <i class="fas fa-edit me-1" aria-hidden="true"></i> Editar
            </x-buttons.link-button>

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

            <x-forms.section title="Disciplinas do Curso" />

            <div class="col-12 p-4" aria-label="Lista de disciplinas do curso">
                @forelse($studentCourse->course->disciplines as $discipline)
                    <div class="border rounded p-3 mb-2">
                        <strong>{{ $discipline->name }}</strong>
                        <div class="text-muted small">
                            {{ $discipline->description ?? 'Sem descrição' }}
                        </div>
                    </div>
                @empty
                    <div class="text-muted">Nenhuma disciplina vinculada ao curso.</div>
                @endforelse
            </div>

            <x-forms.section title="Informações do Sistema" />

            <x-show.info-item label="Criado em" column="col-md-6" isBox="true">
                {{ $studentCourse->created_at->format('d/m/Y H:i') }}
            </x-show.info-item>

            <x-show.info-item label="Atualizado em" column="col-md-6" isBox="true">
                {{ $studentCourse->updated_at->format('d/m/Y H:i') }}
            </x-show.info-item>

            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-badge me-1"></i> ID: #{{ $studentCourse->id }}
                </div>

                <div class="d-flex gap-3">

                    <form
                        action="{{ route('specialized-educational-support.student-courses.destroy', $studentCourse) }}"
                        method="POST"
                        onsubmit="return confirm('Deseja excluir esta matrícula?')"
                        aria-label="Excluir matrícula">
                        @csrf
                        @method('DELETE')

                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt me-1" aria-hidden="true"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                </div>
            </div>

        </div>
    </div>
@endsection