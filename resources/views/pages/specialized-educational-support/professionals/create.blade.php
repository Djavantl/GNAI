@extends('layouts.master')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Profissionais' => route('specialized-educational-support.professionals.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Novo Profissional</h2>
            <p class="text-muted">Cadastre os dados pessoais e funcionais do novo profissional no sistema.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.professionals.index') }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>

    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.professionals.store') }}" method="POST" enctype="multipart/form-data">
            
            <x-forms.section title="Dados da Pessoa" />

            <x-forms.photo-upload
                name="photo"
                label="Foto do Profissional"
            />
            
            <div class="col-md-6">
                <x-forms.input name="name" label="Nome " required :value="old('name')" />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="document" 
                    label="Documento " 
                    required 
                    :value="old('document')" 
                    class="cpf-mask"
                    maxlength="14"  
                    placeholder="000.000.000-00"/>
            </div>

            <div class="col-md-6">
                <x-forms.input name="birth_date" label="Nascimento " type="date" required :value="old('birth_date')" />
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
                <x-forms.input name="email" label="Email " type="email" required :value="old('email')" />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="phone" 
                    label="Telefone" 
                    :value="old('phone')" 
                    class="phone-mask" 
                    maxlength="15" 
                    placeholder="(00) 00000-0000"/>
            </div>

            <div class="col-md-12">
                <x-forms.textarea rows="2" name="address" label="Endereço" :value="old('address')" />
            </div>

            <x-forms.section title="Dados do Profissional" />

            <div class="col-md-6">
                <x-forms.select
                    name="position_id"
                    label="Cargo "
                    required
                    :options="$positions->pluck('name', 'id')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input name="registration" label="Matrícula " required :value="old('registration')" />
            </div>


            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.professionals.index') }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit ">
                    <i class="fas fa-save"></i> Salvar 
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection
@push('scripts')
    @vite(['resources/js/components/photos.js'])
@endpush