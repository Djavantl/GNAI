@extends('layouts.master')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => route('specialized-educational-support.teachers.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Novo Professor</h2>
            <p class="text-muted">Cadastre os dados pessoais, matrícula e atribua as disciplinas do novo docente.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.index') }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.teachers.store') }}" method="POST" enctype="multipart/form-data">
            
            <x-forms.section title="Dados Pessoais" />

            <x-forms.photo-upload
                name="photo"
                label="Foto do Professor"
            />
            
            <div class="col-md-6">
                <x-forms.input name="name" label="Nome Completo" required :value="old('name')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="document" label="CPF/Documento" required :value="old('document')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="birth_date" label="Data de Nascimento" type="date" required :value="old('birth_date')" />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="gender"
                    label="Gênero"
                    :options="[
                        'not_specified' => 'Não informado',
                        'male' => 'Masculino',
                        'female' => 'Feminino',
                        'other' => 'Outro'
                    ]"
                    :value="old('gender', 'not_specified')"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-forms.input name="email" label="E-mail de Acesso" type="email" required :value="old('email')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="phone" label="Telefone" :value="old('phone')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea rows="2" name="address" label="Endereço Residencial" :value="old('address')" />
            </div>

            <x-forms.section title="Dados Docentes" />

            <div class="col-md-6">
                <x-forms.input name="registration" label="Número de Matrícula" required :value="old('registration')" />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.index') }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar Professor
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/components/photos.js'])
    <script>
        // Caso use algum select2 ou similar, inicialize aqui para o campo disciplines[]
    </script>
@endpush