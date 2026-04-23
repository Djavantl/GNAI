@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Responsáveis' => route('specialized-educational-support.guardians.index', $student),
            $guardian->person->name => route('specialized-educational-support.guardians.show', $guardian),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar Responsável</h2>
            <p class="text-muted">Editando vínculo de: <strong>{{ $guardian->person->name }}</strong></p>
        </div>
        <x-buttons.link-button
            href="{{ route('specialized-educational-support.guardians.show', $guardian) }}"
            variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('specialized-educational-support.guardians.update', [$student, $guardian]) }}"
            method="POST"
            enctype="multipart/form-data">
            @method('PUT')

            <x-forms.section title="Dados Pessoais" />

            <x-forms.photo-upload
                name="photo"
                label="Foto do Responsável"
                :current="$guardian->person->photo ? asset('storage/' . $guardian->person->photo) : null"
            />

            <div class="col-md-6">
                <x-forms.input
                    name="name"
                    label="Nome Completo"
                    required
                    :value="old('name', $guardian->person->name)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="document"
                    label="CPF / Documento"
                    required
                    :value="old('document', $guardian->person->document)"
                    class="cpf-mask"
                    maxlength="14"
                    placeholder="000.000.000-00"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    type="date"
                    name="birth_date"
                    label="Data de Nascimento"
                    required
                    :value="old('birth_date', $guardian->person->birth_date ? $guardian->person->birth_date->format('Y-m-d') : '')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="gender"
                    label="Gênero"
                    :options="\App\Models\SpecializedEducationalSupport\Guardian::genderOptions()"
                    :value="old('gender', $guardian->person->gender)"
                    :selected="old('gender', $guardian->person->gender)"
                />
            </div>

            <x-forms.section title="Contato e Vínculo" />

            <div class="col-md-6">
                <x-forms.input
                    type="email"
                    name="email"
                    label="E-mail"
                    :value="old('email', $guardian->person->email)"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="phone"
                    label="Telefone / WhatsApp"
                    :value="old('phone', $guardian->person->phone)"
                    class="phone-mask"
                    maxlength="15"
                    placeholder="(00) 00000-0000"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="relationship"
                    label="Parentesco / Vínculo"
                    required
                    :options="\App\Models\SpecializedEducationalSupport\Guardian::relationshipOptions()"
                    :value="old('relationship', $guardian->relationship)"
                    :selected="old('relationship', $guardian->relationship)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="address"
                    label="Endereço Completo"
                    rows="2"
                    :value="old('address', $guardian->person->address)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4">
                <x-buttons.link-button
                    href="{{ route('specialized-educational-support.guardians.show', $guardian) }}"
                    variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
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
