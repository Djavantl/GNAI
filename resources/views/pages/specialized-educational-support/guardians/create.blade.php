@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Responsáveis' => route('specialized-educational-support.guardians.index', $student),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Cadastrar Responsável</h2>
            <p class="text-muted">Aluno: <strong>{{ $student->person->name }}</strong></p>
        </div>
        <x-buttons.link-button
            href="{{ route('specialized-educational-support.guardians.index', $student) }}"
            variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('specialized-educational-support.guardians.store', $student) }}"
            method="POST"
            enctype="multipart/form-data">

            <x-forms.section title="Dados Pessoais" />

            <x-forms.photo-upload name="photo" label="Foto do Responsável" />

            <div class="col-md-6">
                <x-forms.input
                    name="name"
                    label="Nome Completo"
                    required
                    :value="old('name')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="document"
                    label="CPF"
                    :value="old('document')"
                    class="cpf-mask"
                    maxlength="14"
                    placeholder="000.000.000-00"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    type="date"
                    name="birth_date"
                    label="Data de Nascimento"
                    required
                    :value="old('birth_date')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="gender"
                    label="Gênero"
                    required
                    :options="$genders"
                    :selected="old('gender', $defaultGender)"
                />
            </div>

            <x-forms.section title="Contato e Vínculo" />

            <div class="col-md-6">
                <x-forms.input
                    type="email"
                    name="email"
                    label="E-mail"
                    :value="old('email')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="phone"
                    label="Telefone / WhatsApp"
                    required
                    :value="old('phone')"
                    class="phone-mask"
                    maxlength="15"
                    placeholder="(00) 00000-0000"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="relationship"
                    label="Parentesco / Vínculo"
                    required
                    :options="$relationships"
                    :selected="old('relationship')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="address"
                    label="Endereço Completo"
                    rows="2"
                    maxlength="500"
                    :value="old('address')"
                />
            </div>

            <div class="col-12 d-flex flex-wrap justify-content-end gap-2 border-top pt-4 px-4 pb-4">
                <x-buttons.link-button
                    href="{{ route('specialized-educational-support.guardians.index', $student) }}"
                    variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>

@endsection
@push('scripts')
    @vite(['resources/js/components/photos.js'])
@endpush
