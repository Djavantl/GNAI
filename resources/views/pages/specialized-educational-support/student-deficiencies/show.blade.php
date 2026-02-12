@extends('layouts.master')

@section('title', 'Detalhes da Deficiência')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Deficiências' => route('specialized-educational-support.student-deficiencies.index', $student),
            $deficiency->deficiency->name  => null
        ]" />
    </div>
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes da Deficiência</h2>
            <p class="text-muted">
               Aluno: {{ $student->person->name }}
            </p>
        </div>
       <div class="d-flex gap-2 align-items-start">
            {{-- Forçamos o nome do parâmetro para 'student_deficiency' como na sua Route --}}
            <x-buttons.link-button 
                :href="route('specialized-educational-support.student-deficiencies.edit', $deficiency)" 
                variant="warning"
            >
                <i class="fas fa-edit mr-1"></i> Editar Registro
            </x-buttons.link-button>

            <x-buttons.link-button 
                :href="route('specialized-educational-support.student-deficiencies.index', $student)" 
                variant="secondary"
            >
                Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <x-forms.section title="Informações do Diagnóstico" class="px-4 pt-4" />
                
                <div class="row px-4 pb-4 g-3">
                    <div class="col-md-6">
                        <label class="text-muted small d-block">Deficiência / Condição</label>
                        <span class="fw-bold fs-5">{{ $deficiency->deficiency->name }}</span>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small d-block">Grau de Severidade</label>
                        @php
                            $severityLabels = ['mild' => 'Leve', 'moderate' => 'Moderada', 'severe' => 'Severa'];
                            $severityColors = ['mild' => 'success', 'moderate' => 'warning', 'severe' => 'danger'];
                        @endphp
                        <span class="badge bg-{{ $severityColors[$deficiency->severity] ?? 'secondary' }} text-uppercase">
                            {{ $severityLabels[$deficiency->severity] ?? 'Não Informado' }}
                        </span>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small d-block">Utiliza Recursos de Apoio?</label>
                        <span class="fw-bold {{ $deficiency->uses_support_resources ? 'text-success' : 'text-muted' }}">
                            {{ $deficiency->uses_support_resources ? 'SIM' : 'NÃO' }}
                        </span>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small d-block">Data do Registro</label>
                        <span>{{ $deficiency->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="col-md-12 mt-4">
                        <x-forms.section title="Observações e Notas Técnicas" />
                        <div class="bg-light p-3 rounded border">
                            {!! nl2br(e($deficiency->notes ?? 'Nenhuma observação registrada.')) !!}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">ID do Registro: #{{ $deficiency->id }}</small>
                <form action="{{ route('specialized-educational-support.student-deficiencies.destroy', $deficiency) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-buttons.submit-button variant="danger" onclick="return confirm('Tem certeza que deseja excluir permanentemente este registro?')">
                        <i class="fas fa-trash-alt mr-1"></i> Excluir Registro
                    </x-buttons.submit-button>
                </form>
            </div>
        </div>
    </div>
@endsection