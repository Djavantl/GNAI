@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Profissionais' => route('specialized-educational-support.professionals.index'),
            $professional->person->name => route('specialized-educational-support.professionals.show', $professional),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Profissional</h2>
            <p class="text-muted">Edite os dados pessoais e funcionais do profissional ou tutor de pares. Mesmo com o título “Profissionais”, esta seção permite a atualização de informações de tutores de pares.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.professionals.show', $professional) }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.professionals.update', $professional) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')

            <x-forms.section title="Dados da Pessoa" />

            <x-forms.photo-upload
                name="photo"
                label="Foto do Profissional"
                :current="$professional->person->photo_url"
            />
            
            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome " 
                    required 
                    :value="old('name', $professional->person->name)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="document" 
                    label="CPF"
                    class="cpf-mask"
                    maxlength="14"  
                    placeholder="000.000.000-00" 
                    :value="old('document', $professional->person->document)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="birth_date" 
                    label="Nascimento " 
                    type="date" 
                    required 
                    :value="old('birth_date', optional($professional->person->birth_date)->format('Y-m-d'))" 
                />
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
                    :value="old('gender', $professional->person->gender)"
                    :selected="old('gender', $professional->person->gender)"
                    required
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="phone" 
                    label="Telefone"
                    class="phone-mask" 
                    maxlength="15" 
                    placeholder="(00) 00000-0000" 
                    :value="old('phone', $professional->person->phone)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="email" 
                    label="Email " 
                    type="email" 
                    required 
                    :value="old('email', $professional->person->email)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    rows="2"
                    name="address"
                    label="Endereço"
                    :value="old('address', $professional->person->address)"
                />
            </div>

            <x-forms.section title="Dados do Profissional" />

            <div class="col-md-6">
                <x-forms.select
                    name="position_id"
                    label="Cargo "
                    required
                    :options="$positions->pluck('name', 'id')"
                    :value="old('position_id', $professional->position_id)"
                    :selected="old('position_id', $professional->position_id)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="registration" 
                    label="Matrícula " 
                    required 
                    :value="old('registration', $professional->registration)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status"
                    label="Status"
                    :options="[
                        'active' => 'Ativo',
                        'inactive' => 'Inativo'
                    ]"
                    :value="old('status', $professional->status)"
                    :selected="old('status', $professional->status)"
                    required
                />
            </div>

            @if(auth()->check() && auth()->user()->isAdmin())
                <div class="col-12 mt-3">
                    <x-forms.checkbox 
                        name="is_admin" 
                        label="Definir este profissional como administrador do sistema" 
                        :checked="old('is_admin', optional($professional->user)->is_admin)" 
                    />
                </div>
            @endif

            <div class="col-12 d-flex flex-wrap justify-content-end gap-2 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.professionals.show', $professional) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
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
