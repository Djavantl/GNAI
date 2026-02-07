@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes da Matrícula</h2>
            <p class="text-muted">Informações detalhadas sobre o vínculo do aluno com o curso.</p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.student-courses.edit', $studentCourse)" variant="warning">
                <i class="fas fa-edit"></i> Editar Registro
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.student-courses.index')" variant="secondary">
                Voltar para Lista
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">

            <x-forms.section title="Dados do Aluno" />

            <x-show.info-item label="Nome do Estudante" column="col-md-8" isBox="true">
                <strong>{{ $studentCourse->student->person->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Matrícula Institucional" column="col-md-4" isBox="true">
                {{ $studentCourse->student->registration }}
            </x-show.info-item>

            <x-forms.section title="Informações do Curso" />

            <x-show.info-item label="Curso / Série" column="col-md-6" isBox="true">
                {{ $studentCourse->course->name }}
            </x-show.info-item>

            <x-show.info-item label="Ano Letivo" column="col-md-3" isBox="true">
                {{ $studentCourse->academic_year }}
            </x-show.info-item>

            <x-show.info-item label="Vigência" column="col-md-3" isBox="true">
                @if($studentCourse->is_current)
                    <span class="text-success fw-bold">CURSO ATUAL</span>
                @else
                    <span class="text-muted">HISTÓRICO</span>
                @endif
            </x-show.info-item>

            <x-show.info-item label="Status Acadêmico" column="col-md-6" isBox="true">
                {{ strtoupper($studentCourse->status) }}
            </x-show.info-item>

            <x-show.info-item label="Data de Matrícula" column="col-md-6" isBox="true">
                {{ $studentCourse->created_at->format('d/m/Y H:i') }}
            </x-show.info-item>

            <div class="col-12 border-top p-4 d-flex justify-content-end bg-light no-print">
                <form action="{{ route('specialized-educational-support.student-courses.destroy', $studentCourse) }}"
                      method="POST"
                      onsubmit="return confirm('Deseja remover este registro do histórico?')">
                    @csrf
                    @method('DELETE')
                    <x-buttons.submit-button variant="danger">
                        <i class="fas fa-trash-alt me-1"></i> Remover Registro
                    </x-buttons.submit-button>
                </form>
            </div>
        </div>
    </div>
@endsection
