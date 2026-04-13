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
            @if($teacher->user && auth()->user()->is_admin)
            <form method="POST"
                action="{{ route('admin.impersonate', $teacher->user) }}"
                onsubmit="return confirm('Tem certeza que deseja entrar como {{ $teacher->person->name }}?')">
                @csrf
                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-right-to-bracket"></i> Entrar como
                </x-buttons.submit-button>
            </form>
            @endif
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

            <div class="col-12 mb-4 px-4">

                @php
                    $grouped = $teacher->courseDisciplines
                        ->groupBy(fn($item) => $item->course->name);
                @endphp

                @forelse($grouped as $courseName => $items)

                    <div class="card border-0 shadow-sm rounded-3 mb-4">

                        {{-- Cabeçalho do Curso --}}
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">

                            <div class="fw-bold text-purple-dark">
                                <i class="fas fa-graduation-cap me-2"></i>
                                {{ $courseName }}
                            </div>

                            <span class="badge bg-secondary">
                                {{ $items->count() }}
                                {{ $items->count() === 1 ? 'disciplina' : 'disciplinas' }}
                            </span>

                        </div>

                        {{-- Disciplinas --}}
                        <div class="card-body">

                            <div class="d-flex flex-wrap gap-2">

                                @foreach($items as $assignment)

                                    <span class="badge bg-purple-light text-purple-dark border px-3 py-2">

                                        <i class="fas fa-book me-1"></i>

                                        {{ $assignment->discipline->name }}

                                    </span>

                                @endforeach

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-5 text-muted">

                        <i class="fas fa-book-open mb-3" style="font-size: 32px;"></i>

                        <div class="fw-semibold">
                            Nenhuma disciplina atribuída
                        </div>

                        <div class="small">
                            Utilize o botão <strong>Gerenciar Disciplinas</strong> para realizar as atribuições.
                        </div>

                    </div>

                @endforelse

            </div>

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