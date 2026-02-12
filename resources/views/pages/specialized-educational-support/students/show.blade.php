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
            <h2 class="text-title">Prontuário do Estudante</h2>
            <p class="text-muted">
                Gerenciamento de informações cadastrais e acadêmicas.
            </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.students.edit', $student)" variant="warning">
                <i class="fas fa-edit"></i> Editar Prontuário
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.students.index')" variant="secondary">
                Voltar para Lista
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO 1: DADOS PESSOAIS (Model Person) --}}
            <x-forms.section title="Dados Pessoais" />

            <div class="col-12 d-flex justify-content-center py-4 bg-light mb-4 border-bottom">
                <div class="text-center">
                    <img src="{{ $student->person->photo_url }}" class="avatar-show">
                    <h4 class="mt-3 text-title mb-0">{{ $student->person->name }}</h4>
                    <span class="badge bg-primary">Aluno AEE</span>
                </div>
            </div>
            
            <x-show.info-item label="Nome Completo" column="col-md-8" isBox="true">
                <strong>{{ $student->person->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Gênero" column="col-md-4" isBox="true">
                @php
                    $genders = ['male' => 'Masculino', 'female' => 'Feminino', 'other' => 'Outro'];
                @endphp
                {{ $genders[$student->person->gender] ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="CPF / Documento" column="col-md-4" isBox="true">
                {{ $student->person->document ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Data de Nascimento" column="col-md-4" isBox="true">
                {{ $student->person->birth_date ? \Carbon\Carbon::parse($student->person->birth_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>

            <x-show.info-item label="Idade" column="col-md-4" isBox="true">
                {{ $student->person->birth_date ? \Carbon\Carbon::parse($student->person->birth_date)->age . ' anos' : '---' }}
            </x-show.info-item>

            <x-show.info-item label="E-mail" column="col-md-6" isBox="true">
                {{ $student->person->email ?? 'Nenhum e-mail cadastrado' }}
            </x-show.info-item>

            <x-show.info-item label="Telefone / Contato" column="col-md-6" isBox="true">
                {{ $student->person->phone ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Endereço Residencial" column="col-md-12" isBox="true">
                {{ $student->person->address ?? 'Endereço não informado' }}
            </x-show.info-item>


            {{-- SEÇÃO 2: DADOS ACADÊMICOS (Model Student) --}}
            <x-forms.section title="Informações Escolares" />

            <x-show.info-item label="Matrícula" column="col-md-4" isBox="true">
                <span class="text-purple-dark fw-bold">{{ $student->registration }}</span>
            </x-show.info-item>

            <x-show.info-item label="Status da Matrícula" column="col-md-4" isBox="true">
                @if($student->status === 'active')
                    <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> ATIVO</span>
                @else
                    <span class="text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> {{ strtoupper($student->status) }}</span>
                @endif
            </x-show.info-item>

            <x-show.info-item label="Data de Ingresso no NAPNE" column="col-md-4" isBox="true">
                {{ $student->entry_date ? \Carbon\Carbon::parse($student->entry_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $student->id }}
                </div>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.students.destroy', $student) }}" 
                          method="POST" 
                          onsubmit="return confirm('ATENÇÃO: Esta ação excluirá todos os dados do aluno. Confirmar?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt me-1"></i> Excluir Aluno
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('specialized-educational-support.students.edit', $student)" variant="warning">
                        <i class="fas fa-edit me-1"></i> Editar Prontuário
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection