@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Responsável</h2>
            <p class="text-muted">Aluno: <strong>{{ $student->person->name }}</strong></p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.guardians.store', $student) }}" method="POST">
            
            <x-forms.section title="Dados Pessoais" />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome Completo *" 
                    required 
                    :value="old('name')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="document" 
                    label="CPF/Documento *" 
                    required 
                    :value="old('document')" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    type="date" 
                    name="birth_date" 
                    label="Data de Nascimento *" 
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

            <x-forms.section title="Contato e Vínculo" />

            <div class="col-md-6">
                <x-forms.input 
                    type="email" 
                    name="email" 
                    label="E-mail *" 
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

            <div class="col-md-6">
                <x-forms.select
                    name="relationship"
                    label="Parentesco / Vínculo *"
                    required
                    :options="[
                        'father' => 'Pai',
                        'mother' => 'Mãe',
                        'grandfather' => 'Avô',
                        'grandmother' => 'Avó',
                        'guardian' => 'Responsável Legal',
                        'other' => 'Outro'
                    ]"
                    :value="old('relationship')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="address" 
                    label="Endereço Completo" 
                    rows="2" 
                    :value="old('address')" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.guardians.index', $student) }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Responsável
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection