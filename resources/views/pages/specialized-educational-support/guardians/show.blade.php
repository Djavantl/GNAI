@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Responsáveis' => route('specialized-educational-support.guardians.index', $student),
            $guardian->person->name => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Dados do Responsável</h2>
            <p class="text-muted">
                Responsável por: <strong>{{ $guardian->student->person->name }}</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            @can('guardian.update')
                <x-buttons.link-button
                    :href="route('specialized-educational-support.guardians.edit', [$guardian->student_id, $guardian->id])"
                    variant="warning">
                    <i class="fas fa-edit"></i> Editar
                </x-buttons.link-button>
            @endcan

            <x-buttons.link-button
                :href="route('specialized-educational-support.guardians.index', $guardian->student_id)"
                variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm rounded">
        <div class="row g-0">

            {{-- SEÇÃO: VÍNCULO --}}
            <x-forms.section title="Vínculo Familiar" />

            {{-- Mini perfil com foto --}}
            <div class="col-12 d-flex justify-content-center py-4 bg-light mb-2 border-bottom">
                <div class="text-center">
                    <img src="{{ $guardian->person->photo_url }}"
                         class="avatar-show-lg"
                         alt="Foto de {{ $guardian->person->name }}">
                    <h4 class="mt-3 text-title mb-1">{{ $guardian->person->name }}</h4>
                    <span class="badge bg-secondary px-3 py-2">
                        {{ $guardian->relationshipLabel() }}
                    </span>
                </div>
            </div>

            <x-show.info-item label="Parentesco / Relação" column="col-md-6" isBox="true">
                <strong>{{ $guardian->relationshipLabel() }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Estudante Vinculado" column="col-md-6" isBox="true">
                {{ $guardian->student->person->name }}
            </x-show.info-item>

            {{-- SEÇÃO: DADOS PESSOAIS --}}
            <x-forms.section title="Informações Pessoais" />

            <x-show.info-item label="Nome Completo" column="col-md-8" isBox="true">
                <strong>{{ $guardian->person->name }}</strong>
            </x-show.info-item>

            <x-show.info-item label="Gênero" column="col-md-4" isBox="true">
                {{ \App\Models\SpecializedEducationalSupport\Guardian::genderOptions()[$guardian->person->gender] ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="CPF / Documento" column="col-md-4" isBox="true">
                {{ $guardian->person->document ?? '—' }}
            </x-show.info-item>

            <x-show.info-item label="Data de Nascimento" column="col-md-4" isBox="true">
                {{ $guardian->person->birth_date
                    ? \Carbon\Carbon::parse($guardian->person->birth_date)->format('d/m/Y')
                    : '—' }}
            </x-show.info-item>

            <x-show.info-item label="Idade" column="col-md-4" isBox="true">
                @if($guardian->person->birth_date)
                    {{ \Carbon\Carbon::parse($guardian->person->birth_date)->age }} anos
                @else
                    —
                @endif
            </x-show.info-item>

            {{-- SEÇÃO: CONTATO --}}
            <x-forms.section title="Canais de Contato" />

            <x-show.info-item label="Telefone / WhatsApp" column="col-md-6" isBox="true">
                {{ $guardian->person->phone ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="E-mail" column="col-md-6" isBox="true">
                {{ $guardian->person->email ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="Endereço Residencial" column="col-md-12" isBox="true">
                {{ $guardian->person->address ?? 'Endereço não cadastrado.' }}
            </x-show.info-item>

            {{-- RODAPÉ --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light rounded-bottom no-print">
                <div class="text-muted small">
                    <i class="fas fa-clock me-1"></i>
                    Última atualização: {{ $guardian->updated_at->format('d/m/Y H:i') }}
                </div>

                <div class="d-flex gap-3">
                    @can('guardian.delete')
                        <x-buttons.submit-button
                            type="button"
                            variant="danger"
                            data-bs-toggle="modal"
                            data-bs-target="#globalConfirmActionModal"
                            data-confirm-title="Excluir Responsável"
                            data-confirm-message="Deseja remover este responsável? Esta ação não pode ser desfeita."
                            data-confirm-action="{{ route('specialized-educational-support.guardians.destroy', [$guardian->student_id, $guardian->id]) }}"
                            data-confirm-method="DELETE"
                            data-confirm-submit-text="Confirmar Exclusão"
                            data-confirm-variant="danger"
                        >
                            <i class="fas fa-trash"></i> Excluir
                        </x-buttons.submit-button>
                    @endcan
                </div>
            </div>

        </div>
    </div>
@endsection
