@extends('layouts.master')

@section('title', 'Detalhes da Deficiência')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Deficiências' => route('specialized-educational-support.student-deficiencies.index', $student),
            $deficiency->deficiency->name => null
        ]" />
    </div>

    {{-- Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Detalhes da Deficiência</h2>
            <p class="text-muted">
                Aluno: {{ $student->person->name }}
            </p>
        </div>

        <div class="d-flex gap-2">
            <x-buttons.link-button 
                :href="route('specialized-educational-support.student-deficiencies.edit', ['student' => $student,'student_deficiency' => $deficiency])" 
                variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

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

            <x-show.info-item label="Deficiência / Condição" column="col-md-6" isBox="true">
                {{ $deficiency->deficiency->name }}
            </x-show.info-item>

            <x-show.info-item label="Severidade" column="col-md-6" isBox="true">
                <span class="text-{{ $severityColors[$deficiency->severity] ?? 'secondary' }} fw-bold">
                    {{ $severityLabels[$deficiency->severity] ?? 'Não informada' }}
                </span>
            </x-show.info-item>

            <x-show.info-item label="Utiliza Recursos de Apoio" column="col-md-6" isBox="true">
                {{ $deficiency->uses_support_resources ? 'SIM' : 'NÃO' }}
            </x-show.info-item>

            <x-show.info-item label="Data do Registro" column="col-md-6" isBox="true">
                {{ $deficiency->created_at?->format('d/m/Y H:i') }}
            </x-show.info-item>

            <x-show.info-textarea label="Observações Técnicas" column="col-md-12" isBox="true">
                {{ $deficiency->notes ?? 'Nenhuma observação registrada.' }}
            </x-show.info-textarea>

            {{-- FOOTER PADRÃO --}}
            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-id-card me-1"></i>
                    ID do Registro: #{{ $deficiency->id }}
                </div>

                <div class="d-flex gap-2">
                    <form action="{{ route('specialized-educational-support.student-deficiencies.destroy', ['student' => $student,'student_deficiency' => $deficiency]) }}" 
                          method="POST"
                          onsubmit="return confirm('Deseja excluir permanentemente este registro?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                </div>
            </footer>

        </div>
    </div>
@endsection