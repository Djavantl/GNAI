@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Profissional</h2>
            <p class="text-muted">Atualize as informações do profissional e seu vínculo com a instituição.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.professionals.update', $professional) }}" method="POST">
            @method('PUT')

            <x-forms.section title="Dados da Pessoa" />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome *" 
                    required 
                    :value="old('name', $professional->person->name)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="document" 
                    label="Documento *" 
                    required 
                    :value="old('document', $professional->person->document)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="birth_date" 
                    label="Nascimento *" 
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
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="phone" 
                    label="Telefone" 
                    :value="old('phone', $professional->person->phone)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="email" 
                    label="Email *" 
                    type="email" 
                    required 
                    :value="old('email', $professional->person->email)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="address" 
                    label="Endereço" 
                    :value="old('address', $professional->person->address)" 
                />
            </div>

            <x-forms.section title="Dados do Profissional" />

            <div class="col-md-6">
                <x-forms.select
                    name="position_id"
                    label="Cargo *"
                    required
                    :options="$positions->pluck('name', 'id')"
                    :value="old('position_id', $professional->position_id)"
                    :selected="old('position_id', $professional->position_id)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="registration" 
                    label="Matrícula *" 
                    required 
                    :value="old('registration', $professional->registration)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="entry_date" 
                    label="Entrada *" 
                    type="date" 
                    required 
                    :value="old('entry_date', $professional->entry_date)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="status"
                    label="Status"
                    :options="['active' => 'Ativo', 'inactive' => 'Inativo']"
                    :value="old('status', $professional->status)"
                    :selected="old('status', $professional->status)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.professionals.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-sync mr-2"></i> Atualizar Profissional
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection