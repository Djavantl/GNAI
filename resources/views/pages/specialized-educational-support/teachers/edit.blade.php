@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => route('specialized-educational-support.teachers.index'),
            $teacher->person->name => route('specialized-educational-support.teachers.show', $teacher),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Professor</h2>
            <p class="text-muted">Atualize os dados e as disciplinas atribuídas ao docente.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.show', $teacher) }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')

            <x-forms.section title="Dados Pessoais" />

            <x-forms.photo-upload
                name="photo"
                label="Foto do Professor"
                :current="$teacher->person->photo_url"
            />
            
            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome Completo *" 
                    required 
                    :value="old('name', $teacher->person->name)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="document" 
                    label="CPF/Documento *" 
                    required 
                    :value="old('document', $teacher->person->document)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="birth_date" 
                    label="Data de Nascimento *" 
                    type="date" 
                    required 
                    :value="old('birth_date', optional($teacher->person->birth_date)->format('Y-m-d'))" 
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
                    :value="old('gender', $teacher->person->gender)"
                    :selected="old('gender', $teacher->person->gender)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="phone" 
                    label="Telefone" 
                    :value="old('phone', $teacher->person->phone)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="email" 
                    label="E-mail *" 
                    type="email" 
                    required 
                    :value="old('email', $teacher->person->email)" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    rows="2"
                    name="address" 
                    label="Endereço" 
                    :value="old('address', $teacher->person->address)" 
                />
            </div>

            <x-forms.section title="Dados Docentes" />

            <div class="col-md-6">
                <x-forms.input 
                    name="registration" 
                    label="Matrícula *" 
                    required 
                    :value="old('registration', $teacher->registration)" 
                />
            </div>

            <div class="col-md-6">
                {{-- No edit, o value/selected deve ser o array de IDs já vinculados --}}
                <x-forms.select
                    name="disciplines[]"
                    label="Disciplinas Lecionadas *"
                    required
                    multiple
                    :options="$disciplines->pluck('name', 'id')"
                    :value="old('disciplines', $selectedDisciplines)"
                    :selected="old('disciplines', $selectedDisciplines)"
                />
                <small class="text-muted">Pressione Ctrl/Cmd para gerenciar as disciplinas.</small>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.show', $teacher) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Atualizar Professor
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/components/photos.js'])
@endpush