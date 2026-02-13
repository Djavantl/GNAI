@extends('layouts.master')

@section('title', 'Planos Educacionais Individualizados (PEI)')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'PEIs' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Histórico de PEIs</h2>
            <p class="text-muted">Aluno: {{ $student->person->name }}</p>
        </div>
        <x-buttons.link-button
            :href="route('specialized-educational-support.pei.create', $student->id)"
            variant="new"
        >
            Novo PEI
        </x-buttons.link-button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <x-table.table :headers="['Semestre', 'Componente Curricular', 'Docente Responsável', 'Status', 'Ações']">
        @forelse($peis as $pei)
            <tr>
                <x-table.td>
                    <strong>{{ $pei->semester->label ?? 'N/A' }}</strong><br>
                    <small class="text-muted">Criado em: {{ $pei->created_at->format('d/m/Y') }}</small>
                </x-table.td>

                <x-table.td>
                    <span class="fw-bold text-primary">{{ $pei->discipline->name ?? 'Não informada' }}</span><br>
                    <small class="text-muted">{{ $pei->course->name ?? '' }}</small>
                </x-table.td>

                <x-table.td>
                    {{ $pei->teacher_name }}
                </x-table.td>

                <x-table.td>
                    @if($pei->is_finished)
                        <span class="text-success">
                            FINALIZADO
                        </span>
                    @else
                        <span class="text-warning">
                            EM PREENCHIMENTO
                        </span>
                    @endif
                </x-table.td>

                <x-table.td>
                    <x-table.actions>
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pei.show', $pei->id)"
                            variant="info"
                        >
                            Ver 
                        </x-buttons.link-button>

                    </x-table.actions>
                </x-table.td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-5">
                    <i class="fas fa-clipboard-list d-block mb-2" style="font-size: 2rem;"></i>
                    Nenhum PEI registrado para este aluno nas disciplinas atuais.
                </td>
            </tr>
        @endforelse
    </x-table.table>

    <div class="mt-4">
        <x-buttons.link-button
            :href="route('specialized-educational-support.students.show', $student)"
            variant="secondary"
        >
            <i class="fas fa-chevron-left mr-1"></i> Voltar para Aluno
        </x-buttons.link-button>
    </div>
@endsection