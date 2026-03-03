@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Aluno</h2>
            <p class="text-muted">Insira as informações pessoais e acadêmicas para registrar o novo estudante no sistema.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.students.index') }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.students.store') }}" method="POST" enctype="multipart/form-data">
            
            <x-forms.section title="Dados Pessoais" />

            <x-forms.photo-upload
                name="photo"
                label="Foto do Aluno"
            />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome Completo " 
                    required 
                    :value="old('name')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="document" 
                    label="Documento " 
                    class="cpf-mask"
                    maxlength="14"  
                    placeholder="000.000.000-00"
                    required 
                    :value="old('document')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="birth_date" 
                    label="Data de Nascimento " 
                    type="date" 
                    required 
                    :value="old('birth_date')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="gender"
                    label="Gênero"
                    :options="[
                        'male' => 'Masculino',
                        'female' => 'Feminino',
                        'other' => 'Outro',
                        'not_specified' => 'Não informado'
                    ]"
                    :value="old('gender', 'not_specified')"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="email" 
                    label="E-mail " 
                    type="email" 
                    required 
                    :value="old('email')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="phone" 
                    label="Telefone" 
                    class="phone-mask" 
                    maxlength="15" 
                    placeholder="(00) 00000-0000"
                    :value="old('phone')" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="address"
                    label="Endereço"
                    rows="2"
                    :value="old('address')"
                />
            </div>

            <x-forms.section title="Dados Acadêmicos" />

            <div class="col-md-6">
                <x-forms.input 
                    name="registration" 
                    label="Matrícula " 
                    required 
                    :value="old('registration')" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.students.index') }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i>Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    @push('scripts')
        @vite(['resources/js/components/photos.js'])
    @endpush
@endsection
