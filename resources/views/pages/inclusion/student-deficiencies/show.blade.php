@extends('layouts.master')

@section('title', 'Perfis de Atendimento')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Perfis de Atendimento' => route('specialized-educational-support.student-deficiencies.index', $student),
            $deficiency->deficiency->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes do Perfil de Atendimento do Aluno</h2>
            <p class="text-muted">
                Aluno: {{ $student->person->name }}
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
            @can('student-deficiency.update')
            <x-buttons.link-button
                :href="route('specialized-educational-support.student-deficiencies.edit', ['student' => $student,'student_deficiency' => $deficiency])" 
                variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>
            @endcan
            <x-buttons.link-button 
                :href="route('specialized-educational-support.student-deficiencies.index', $student)" 
                variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm overflow-hidden">
        <div class="row g-0">

            {{-- INFORMAÇÕES DO REGISTRO --}}
            <x-forms.section title="Informações do Diagnóstico" />

            @php
                $severityLabels = [
                    'mild' => 'Leve',
                    'moderate' => 'Moderada',
                    'severe' => 'Severa'
                ];

                $severityColors = [
                    'mild' => 'success',
                    'moderate' => 'warning',
                    'severe' => 'danger'
                ];
            @endphp

            <x-show.info-item label="Nome do Perfil" column="col-md-6" isBox="true">
                {{ $deficiency->deficiency->name }}
            </x-show.info-item>

            <x-show.info-item label="Severidade" column="col-md-6" isBox="true">
                <span class="text-{{ $severityColors[$deficiency->severity] ?? 'secondary' }} fw-bold">
                    {{ $severityLabels[$deficiency->severity] ?? 'Não informada' }}
                </span>
            </x-show.info-item>

            <x-show.info-item label="Data do Registro" column="col-md-6" isBox="true">
                {{ $deficiency->created_at?->format('d/m/Y H:i') }}
            </x-show.info-item>

            <x-show.info-textarea label="Observações Técnicas" column="col-md-12" isBox="true">
                {{ $deficiency->notes ?? 'Nenhuma observação registrada.' }}
            </x-show.info-textarea>

            {{-- FOOTER PADRÃO --}}
            <footer class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light-subtle">
                <div class="d-flex flex-wrap gap-2">
                    @can('student-deficiency.delete')
                    <x-buttons.submit-button
                        type="button"
                        variant="danger"
                        data-bs-toggle="modal"
                        data-bs-target="#globalConfirmActionModal"
                        data-confirm-title="Excluir Perfil de Atendimento"
                        data-confirm-message="Deseja excluir permanentemente este registro?"
                        data-confirm-action="{{ route('specialized-educational-support.student-deficiencies.destroy', ['student' => $student,'student_deficiency' => $deficiency]) }}"
                        data-confirm-method="DELETE"
                        data-confirm-submit-text="Confirmar Exclusao"
                        data-confirm-variant="danger"
                    >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    @endcan
                </div>
            </footer>

        </div>
    </div>
@endsection
