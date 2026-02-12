@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Aluno</h2>
            <p class="text-muted">Atualize as informações cadastrais e acadêmicas do estudante.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.students.update', $student) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')

            <x-forms.section title="Dados Pessoais" />

            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold d-block">Foto do Aluno</label>
                <div class="photo-preview-container">
                    <img src="{{ $student->person->photo_url }}" class="img-preview mb-2" id="preview">
                </div>
                <input type="file" name="photo" class="form-control" accept="image/*">
                
                @if($student->person->photo)
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="removePhoto">
                        <label class="form-check-label text-danger" for="removePhoto">
                            <i class="bi bi-trash"></i> Remover foto atual
                        </label>
                    </div>
                @endif
            </div>

            <div class="col-md-12">
                <x-forms.input 
                    name="name" 
                    label="Nome Completo *" 
                    required 
                    :value="old('name', $student->person->name)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.input 
                    name="document" 
                    label="Documento *" 
                    required 
                    :value="old('document', $student->person->document)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="birth_date" 
                    label="Data de Nascimento *" 
                    type="date" 
                    required 
                    :value="old('birth_date', optional($student->person->birth_date)->format('Y-m-d'))" 
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
                    :value="old('gender', $student->person->gender)"
                    :selected="old('gender', $student->person->gender)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="email" 
                    label="E-mail *" 
                    type="email" 
                    required 
                    :value="old('email', $student->person->email)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="phone" 
                    label="Telefone" 
                    :value="old('phone', $student->person->phone)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="address"
                    label="Endereço"
                    rows="2"
                    :value="old('address', $student->person->address)"
                />
            </div>

            <x-forms.section title="Dados Acadêmicos" />

            <div class="col-md-6">
                <x-forms.input 
                    name="registration" 
                    label="Matrícula *" 
                    required 
                    :value="old('registration', $student->registration)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="entry_date" 
                    label="Data de Ingresso *" 
                    type="date" 
                    required 
                    :value="old('entry_date', $student->entry_date)" 
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.students.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-sync mr-2"></i> Atualizar Cadastro
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection