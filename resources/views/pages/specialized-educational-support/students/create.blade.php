@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Novo Aluno</h2>
            <p class="text-muted">Insira as informações pessoais e acadêmicas para registrar o novo estudante no sistema.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.students.store') }}" method="POST">
            
            <x-forms.section title="Dados Pessoais" />

            <div class="col-md-12">
                <x-forms.input 
                    name="name" 
                    label="Nome Completo *" 
                    required 
                    :value="old('name')" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.input 
                    name="document" 
                    label="Documento *" 
                    required 
                    :value="old('document')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="birth_date" 
                    label="Data de Nascimento *" 
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
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="email" 
                    label="E-mail *" 
                    type="email" 
                    required 
                    :value="old('email')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="phone" 
                    label="Telefone" 
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
                    label="Matrícula *" 
                    required 
                    :value="old('registration')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="entry_date" 
                    label="Data de Ingresso *" 
                    type="date" 
                    required 
                    :value="old('entry_date')" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.students.index') }}" variant="secondary">
                    Voltar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection