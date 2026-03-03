@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => route('specialized-educational-support.teachers.index'),
            $teacher->person->name => null
        ]" />
    </div>

    {{-- Cabeçalho da Página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Perfil do Professor</h2>
            <p class="text-muted">
                Informações detalhadas do docente e atribuições acadêmicas.
            </p>
        </div>
        <div class="d-flex gap-2">
            {{-- Novo Botão de Disciplinas --}}
            <x-buttons.link-button :href="route('specialized-educational-support.teachers.disciplines', $teacher->id)" variant="info">
                <i class="fas fa-book"></i> Gerenciar Disciplinas
            </x-buttons.link-button>
            <x-buttons.link-button :href="route('specialized-educational-support.teachers.edit', $teacher->id)" variant="warning">
                <i class="fas fa-edit"></i> Editar Perfil
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.teachers.index')" variant="secondary">
               <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: DADOS PESSOAIS --}}
            <x-forms.section title="Identificação Pessoal" />

            <div class="col-12 d-flex justify-content-center py-4 bg-light mb-4 border-bottom">
                <div class="text-center">
                    <img src="{{ $teacher->person->photo_url }}" class="avatar-show-lg">
                    <h4 class="mt-3 text-title mb-0">
                        {{ $teacher->person->name }}
                    </h4>
                    <p class="text-muted small">Professor(a) / Docente</p>
                </div>
            </div>
            
            <x-show.info-item label="Nome Completo" column="col-md-8" isBox="true">
                <strong>{{ $teacher->person->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="CPF / Documento" column="col-md-4" isBox="true">
                {{ $teacher->person->document ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Data de Nascimento" column="col-md-4" isBox="true">
                {{ $teacher->person->birth_date ? $teacher->person->birth_date->format('d/m/Y') : '---' }}
            </x-show.info-item>

            <x-show.info-item label="E-mail de Contato" column="col-md-4" isBox="true">
                {{ $teacher->person->email ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Telefone" column="col-md-4" isBox="true">
                {{ $teacher->person->phone ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Endereço" column="col-md-12" isBox="true">
                {{ $teacher->person->address ?? '---' }}
            </x-show.info-item>

            {{-- SEÇÃO: DADOS DOCENTES --}}
            <x-forms.section title="Vínculo Docente" />

            <x-show.info-item label="Matrícula" column="col-md-4" isBox="true">
                <code class="fw-bold">{{ $teacher->registration }}</code>
            </x-show.info-item>

            <x-show.info-item label="Data de Cadastro" column="col-md-4" isBox="true">
                {{ $teacher->created_at->format('d/m/Y') }}
            </x-show.info-item>

            <x-show.info-item label="Status de Acesso" column="col-md-4" isBox="true">
                {{-- No seu caso, o status costuma estar atrelado ao User ou Teacher --}}
                <span class="text-success fw-bold">ATIVO</span>
            </x-show.info-item>

            <x-show.info-item label="Disciplinas Atribuídas" column="col-md-12" isBox="true">
                <div class="d-flex flex-wrap gap-2">
                    @forelse($teacher->disciplines as $discipline)
                        <span class="badge bg-purple-light text-purple-dark border px-3 py-2">
                            <i class="fas fa-book me-1"></i> {{ $discipline->name }}
                        </span>
                    @empty
                        <span class="text-muted small italic">Nenhuma disciplina vinculada.</span>
                    @endforelse
                </div>
            </x-show.info-item>

            {{-- RODAPÉ --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-chalkboard-teacher me-1"></i> Professor ID: #{{ $teacher->id }}
                </div>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.teachers.destroy', $teacher->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Excluir permanentemente este professor e seu acesso ao sistema?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir Registro
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('specialized-educational-support.teachers.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection