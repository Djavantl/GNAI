@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Profissionais' => route('specialized-educational-support.professionals.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Profissional</h2>
            <p class="text-muted">Cadastre os dados pessoais e funcionais do novo profissional no sistema.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.professionals.store') }}" method="POST" enctype="multipart/form-data">
            
            <x-forms.section title="Dados da Pessoa" />

            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Foto do Profissional</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                <small class="text-muted">Formatos aceitos: JPG, PNG. Máximo 2MB.</small>
            </div>

            <div class="col-md-6">
                <x-forms.input name="name" label="Nome *" required :value="old('name')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="document" label="Documento *" required :value="old('document')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="birth_date" label="Nascimento *" type="date" required :value="old('birth_date')" />
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
                />
            </div>

            <div class="col-md-6">
                <x-forms.input name="phone" label="Telefone" :value="old('phone')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="email" label="Email *" type="email" required :value="old('email')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea rows="2" name="address" label="Endereço" :value="old('address')" />
            </div>

            <x-forms.section title="Dados do Profissional" />

            <div class="col-md-6">
                <x-forms.select
                    name="position_id"
                    label="Cargo *"
                    required
                    :options="$positions->pluck('name', 'id')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input name="registration" label="Matrícula *" required :value="old('registration')" />
            </div>

            <div class="col-md-6">
                <x-forms.input name="entry_date" label="Entrada *" type="date" required :value="old('entry_date')" />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status"
                    label="Status"
                    :options="['active' => 'Ativo', 'inactive' => 'Inativo']"
                    :value="old('status', 'active')"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.professionals.index') }}" variant="secondary">
                    Voltar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Profissional
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection