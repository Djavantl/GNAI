@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Profissionais' => route('specialized-educational-support.professionals.index'),
            $professional->person->name => null

        ]" />
    </div>

    {{-- Cabeçalho da Página --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 no-print">
        <div>
            <h2 class="text-title">Perfil do Profissional</h2>
            <p class="text-muted">
                Informações de cadastro e vínculo institucional.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap justify-content-end ms-md-auto">
            @can('professional.update')
            <x-buttons.link-button :href="route('specialized-educational-support.professionals.edit', $professional->id)" variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>
            @endcan

            <x-buttons.link-button :href="route('specialized-educational-support.professionals.index')" variant="secondary">
               <i class="fas fa-arrow-left "></i>  Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: DADOS PESSOAIS --}}
            <x-forms.section title="Identificação Pessoal" />

            <div class="col-12 d-flex justify-content-center py-4 bg-light mb-4 border-bottom">
                <div class="text-center">

                    <img src="{{ $professional->person->photo_url }}"
                        class="avatar-show-lg">

                    <h4 class="mt-3 text-title mb-0">
                        {{ $professional->person->name }}
                    </h4>

                </div>
            </div>
            
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
                    <span class="text-success fw-bold">ATIVO</span>
                @else
                    <span class="text-danger fw-bold">INATIVO</span>
                @endif
            </x-show.info-item>

            <x-show.info-item label="Data de Admissão" column="col-md-6" isBox="true">
                {{ $professional->entry_date ? \Carbon\Carbon::parse($professional->entry_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>

            <x-show.info-item label="Tempo de Instituição" column="col-md-6" isBox="true">
                {{ $professional->entry_date ? \Carbon\Carbon::parse($professional->entry_date)->diffForHumans(null, true) : '---' }}
            </x-show.info-item>

            @if(auth()->check() && auth()->user()->isAdmin())
                <x-show.info-item label="Administrador do Sistema" column="col-md-6" isBox="true">
                    @if(optional($professional->user)->is_admin)
                        <span class="text-success fw-bold">
                            <i class="fas fa-user-shield"></i> SIM
                        </span>
                    @else
                        <span class="text-muted fw-bold">
                            NÃO
                        </span>
                    @endif
                </x-show.info-item>
            @endif

            {{-- RODAPÉ --}}
            <div class="col-12 border-top p-4 d-flex flex-wrap justify-content-end gap-2 bg-light no-print">
                <div class="d-flex flex-wrap gap-2">
                    @can('professional.delete')
                        <x-buttons.submit-button
                            type="button"
                            variant="danger"
                            data-bs-toggle="modal"
                            data-bs-target="#globalConfirmActionModal"
                            data-confirm-title="Excluir Profissional"
                            data-confirm-message="Excluir este profissional do sistema?"
                            data-confirm-action="{{ route('specialized-educational-support.professionals.destroy', $professional->id) }}"
                            data-confirm-method="DELETE"
                            data-confirm-submit-text="Confirmar Exclusao"
                            data-confirm-variant="danger"
                        >
                                <i class="fas fa-trash-alt"></i> Excluir
                            </x-buttons.submit-button>
                    @endcan
                    <x-buttons.link-button :href="route('specialized-educational-support.professionals.index')" variant="secondary">
                        <i class="fas fa-arrow-left "></i>  Voltar
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
