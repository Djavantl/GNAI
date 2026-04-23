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
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
    <div>
        <h2 class="text-title">Prontuário do Aluno</h2>
        <p class="text-muted">Visualize o ecossistema completo e histórico detalhado do aluno.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
        {{-- Permissão para EDITAR o cadastro do aluno --}}
        @can('student.view')
            <x-buttons.pdf-button 
                :href="route('specialized-educational-support.students.pdf', $student)" 
                target="_blank" 
            />
        @endcan

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
                   
                    <span class="badge bg-{{ $student->status?->color() ?? 'secondary' }}">
                        {{ $student->status?->label() ?? '—' }}
                    </span>
                    
                </div>
                <h4 class="mt-2 text-title mb-0">{{ $student->person->name }}</h4>
                <p class="text-muted small">Matrícula: {{ $student->registration }}</p>
            </div>
        </div>

        {{-- Dados básicos e Acadêmicos (Geralmente vinculados à view do estudante) --}}
        @include('pages.specialized-educational-support.students.record.personal-data')
        
        @include('pages.specialized-educational-support.students.record.academic-info')

        {{-- Seção de Perfis de Atendimento --}}
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
        @canany(['session-record.view-all', 'session-record.view-own'])
            @include('pages.specialized-educational-support.students.record.session-records')
        @endcanany
        

        {{-- RODAPÉ DE AÇÕES --}}
        <div class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light no-print">
            <div class="d-flex flex-wrap gap-2">
                {{-- Logs (Conforme solicitado, sem middleware específico, mas pode-se usar student.view) --}}
                {{-- Permissão para EXCLUIR o aluno --}}
                @can('student.delete')
                    <x-buttons.submit-button
                        type="button"
                        variant="danger"
                        data-bs-toggle="modal"
                        data-bs-target="#globalConfirmActionModal"
                        data-confirm-title="Excluir Aluno"
                        data-confirm-message="Excluir este aluno?"
                        data-confirm-action="{{ route('specialized-educational-support.students.destroy', $student) }}"
                        data-confirm-method="DELETE"
                        data-confirm-submit-text="Confirmar Exclusao"
                        data-confirm-variant="danger"
                    >
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
