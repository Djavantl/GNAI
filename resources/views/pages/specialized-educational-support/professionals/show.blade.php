@extends('layouts.app')

@section('content')
    {{-- Cabeçalho da Página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Perfil do Profissional</h2>
            <p class="text-muted">
                Informações de cadastro e vínculo institucional.
            </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.professionals.edit', $professional->id)" variant="warning">
                <i class="fas fa-edit"></i> Editar Perfil
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.professionals.index')" variant="secondary">
                Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: DADOS PESSOAIS --}}
            <x-forms.section title="Identificação Pessoal" />
            
            <x-show.info-item label="Nome Completo" column="col-md-8" isBox="true">
                <strong>{{ $professional->person->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="CPF" column="col-md-4" isBox="true">
                {{ $professional->person->document ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Data de Nascimento" column="col-md-4" isBox="true">
                {{ $professional->person->birth_date ? \Carbon\Carbon::parse($professional->person->birth_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>

            <x-show.info-item label="E-mail" column="col-md-4" isBox="true">
                {{ $professional->person->email ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Telefone" column="col-md-4" isBox="true">
                {{ $professional->person->phone ?? '---' }}
            </x-show.info-item>

            {{-- SEÇÃO: DADOS PROFISSIONAIS --}}
            <x-forms.section title="Vínculo Profissional" />

            <x-show.info-item label="Cargo / Função" column="col-md-6" isBox="true">
                <span class="text-purple-dark fw-bold">
                    {{ $professional->position->name ?? 'Não definido' }}
                </span>
            </x-show.info-item>

            <x-show.info-item label="Matrícula" column="col-md-3" isBox="true">
                {{ $professional->registration }}
            </x-show.info-item>

            <x-show.info-item label="Status" column="col-md-3" isBox="true">
                @if($professional->status === 'active')
                    <span class="text-success fw-bold"><i class="fas fa-user-check"></i> ATIVO</span>
                @else
                    <span class="text-danger fw-bold"><i class="fas fa-user-slash"></i> INATIVO</span>
                @endif
            </x-show.info-item>

            <x-show.info-item label="Data de Admissão" column="col-md-6" isBox="true">
                {{ $professional->entry_date ? \Carbon\Carbon::parse($professional->entry_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>

            <x-show.info-item label="Tempo de Instituição" column="col-md-6" isBox="true">
                {{ $professional->entry_date ? \Carbon\Carbon::parse($professional->entry_date)->diffForHumans(null, true) : '---' }}
            </x-show.info-item>

            {{-- RODAPÉ --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-id-badge me-1"></i> Profissional ID: #{{ $professional->id }}
                </div>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.professionals.destroy', $professional->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Excluir este profissional do sistema?')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt me-1"></i> Remover Profissional
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('specialized-educational-support.professionals.edit', $professional->id)" variant="warning">
                        <i class="fas fa-edit me-1"></i> Editar Dados
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection