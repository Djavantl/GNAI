@extends('layouts.app')

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
    {{-- Cabeçalho da Página --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2 class="text-title">Dados do Responsável</h2>
            <p class="text-muted">
                Responsável por: <strong>{{ $guardian->student->person->name }}</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <x-buttons.link-button :href="route('specialized-educational-support.guardians.edit', [$guardian->student_id, $guardian->id])" variant="warning">
                <i class="fas fa-edit"></i> Editar 
            </x-buttons.link-button>

            <x-buttons.link-button :href="route('specialized-educational-support.guardians.index', $guardian->student_id)" variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar 
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white shadow-sm">
        <div class="row g-0">
            
            {{-- SEÇÃO: VÍNCULO --}}
            <x-forms.section title="Vínculo Familiar" />
            
            <x-show.info-item label="Parentesco / Relação" column="col-md-6" isBox="true">
                <span class="text-purple-dark fw-bold text-uppercase">
                    {{ $guardian->relationship }}
                </span>
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
                @php
                    $genders = \App\Models\SpecializedEducationalSupport\Guardian::genderOptions();
                @endphp
                {{ $genders[$guardian->person->gender] ?? 'Não informado' }}
            </x-show.info-item>

            <x-show.info-item label="CPF / Documento" column="col-md-4" isBox="true">
                {{ $guardian->person->document ?? '---' }}
            </x-show.info-item>

            <x-show.info-item label="Data de Nascimento" column="col-md-4" isBox="true">
                {{ $guardian->person->birth_date ? \Carbon\Carbon::parse($guardian->person->birth_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>

            <x-show.info-item label="Idade Atual" column="col-md-4" isBox="true">
                @if($guardian->person->birth_date)
                    {{ \Carbon\Carbon::parse($guardian->person->birth_date)->age }} anos
                @else
                    ---
                @endif
            </x-show.info-item>

            {{-- SEÇÃO: CONTATO E ENDEREÇO --}}
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
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light no-print">
                <div class="text-muted small">
                    <i class="fas fa-clock me-1"></i> 
                    Última atualização do cadastro: {{ $guardian->updated_at->format('d/m/Y H:i') }}
                </div>
                
                <div class="d-flex gap-3">
                    <form action="{{ route('specialized-educational-support.guardians.destroy', [$guardian->student_id, $guardian->id]) }}" 
                          method="POST" 
                          onsubmit="return confirm('Remover este responsável? Os dados pessoais da pessoa não serão excluídos, apenas o vínculo com o aluno.')">
                        @csrf
                        @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection