@extends('layouts.app')

@section('content')
<div class="mb-5">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Alunos' => route('specialized-educational-support.students.index'),
        $student->person->name => null
    ]" />
</div>

{{-- Cabeçalho da Página --}}
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h2 class="text-title">Prontuário do Aluno</h2>
        <p class="text-muted">Visualize o ecossistema completo e histórico detalhado do aluno.</p>
    </div>
    <div class="d-flex gap-2">
        {{-- Permissão para EDITAR o cadastro do aluno --}}
        @can('student.update')
            <x-buttons.link-button :href="route('specialized-educational-support.students.edit', $student)" variant="warning">
                <i class="fas fa-edit"></i> Editar Cadastro
            </x-buttons.link-button>
        @endcan

        <x-buttons.link-button :href="route('specialized-educational-support.students.index')" variant="secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>
    </div>
</div>

<div class="custom-table-card bg-white shadow-sm">
    <div class="row g-0">

        {{-- SEÇÃO: IDENTIFICAÇÃO --}}
        <x-forms.section title="Identificação do Aluno" />

        <div class="col-12 d-flex justify-content-center py-4 bg-light mb-4 border-bottom">
            <div class="text-center position-relative">
                <img src="{{ $student->person->photo_url }}" class="avatar-show-lg">
                <div class="mt-2">
                    @if($student->status === 'active')
                        <span class="badge bg-success">ATIVO</span>
                    @else
                        <span class="badge bg-danger">{{ strtoupper($student->status) }}</span>
                    @endif
                </div>
                <h4 class="mt-2 text-title mb-0">{{ $student->person->name }}</h4>
                <p class="text-muted small">Matrícula: {{ $student->registration }}</p>
            </div>
        </div>

        {{-- Dados básicos e Acadêmicos (Geralmente vinculados à view do estudante) --}}
        @include('pages.specialized-educational-support.students.record.personal-data')
        
        @include('pages.specialized-educational-support.students.record.academic-info')

        {{-- Seção de Deficiências --}}
        @can('student-deficiency.view')
            @include('pages.specialized-educational-support.students.record.deficiencies')
        @endcan

        {{-- Seção de Responsáveis --}}
        @can('guardian.view')
            @include('pages.specialized-educational-support.students.record.guardians')
        @endcan

        {{-- Seção de Contextos --}}
        @can('student-context.view')
            @include('pages.specialized-educational-support.students.record.contexts')
        @endcan
            
        {{-- Seção de PEIs --}}
        @can('pei.view')
            @include('pages.specialized-educational-support.students.record.peis')
        @endcan
            
        {{-- Seção de Documentos --}}
        @can('student-document.view')
            @include('pages.specialized-educational-support.students.record.documents')
        @endcan
            
        {{-- Seção de Sessões --}}
        @can('session.view')
            @include('pages.specialized-educational-support.students.record.sessions')
        @endcan
        

        {{-- RODAPÉ DE AÇÕES --}}
        <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
            <div class="text-muted small">
                <i class="fas fa-id-badge me-1"></i> Aluno ID: #{{ $student->id }} | Sistema GNAI 2026
            </div>
            
            <div class="d-flex gap-3">
                {{-- Logs (Conforme solicitado, sem middleware específico, mas pode-se usar student.view) --}}
                {{-- Permissão para EXCLUIR o aluno --}}
                @can('student.delete')
                    <form action="{{ route('specialized-educational-support.students.destroy', $student) }}" method="POST" onsubmit="return confirm('Excluir este aluno?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection