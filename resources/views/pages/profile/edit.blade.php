@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Meu Perfil' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Meu Perfil</h2>
            <p class="text-muted">Gerencie suas informações pessoais e segurança da conta.</p>
        </div>
    </div>

    <div class="mt-3">
        {{-- Usamos o seu form-card padrão --}}
        <x-forms.form-card action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            <div style="position: absolute; left: -9999px; top: -9999px;">
                <input type="email" name="fake_email_to_prevent_autofill">
                <input type="password" name="fake_password_to_prevent_autofill">
            </div>

            <div class="row g-0">
                {{-- COLUNA ESQUERDA: Identificação (Não editável) --}}
                <div class="col-md-4 bg-light border-end p-4 text-center">
                    <div class="py-4">
                        <x-forms.photo-upload
                            name="photo"
                            label="Alterar Foto"
                            :current="$person->photo_url"
                        />
                        
                        <h4 class="text-title mt-3 mb-1">{{ $person->name }}</h4>
                        <span class="badge bg-purple-light text-purple-dark mb-4">
                            {{ isset($professional) ? $professional->position->name : 'Professor(a)' }}
                        </span>

                        <div class="text-start mt-4 bg-white p-3 rounded shadow-sm">
                            <div class="mb-3">
                                <label class="text-muted small d-block text-uppercase fw-bold">Matrícula</label>
                                <span class="fw-bold text-dark">{{ $professional->registration ?? $teacher->registration }}</span>
                            </div>
                            <div>
                                <label class="text-muted small d-block text-uppercase fw-bold">Vínculo</label>
                                <span class="text-dark">{{ isset($professional) ? 'Profissional Apoio' : 'Corpo Docente' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- COLUNA DIREITA: Campos Editáveis --}}
                <div class="col-md-8 p-4">
                    <x-forms.section title="Dados Pessoais" />
                    
                    <div class="row">
                        <div class="col-md-12">
                            <x-forms.input name="name" label="Nome Completo" required :value="old('name', $person->name)" />
                        </div>

                        <div class="col-md-6">
                            <x-forms.input 
                                name="document" 
                                label="CPF/Documento" 
                                required 
                                :value="old('document', $person->document)" 
                                class="cpf-mask"
                                maxlength="14"  
                                placeholder="000.000.000-00"/>
                        </div>

                        <div class="col-md-6">
                            <x-forms.input name="birth_date" label="Data de Nascimento" type="date" required :value="old('birth_date', optional($person->birth_date)->format('Y-m-d'))" />
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
                                :value="old('gender', $person->gender)"
                                :selected="old('gender', $person->gender)"
                                required
                            />
                        </div>

                        <div class="col-md-6">
                            <x-forms.input 
                                name="phone" 
                                label="Telefone" 
                                :value="old('phone', $person->phone)" 
                                class="phone-mask" 
                                maxlength="15" 
                                placeholder="(00) 00000-0000"/>
                        </div>

                        <div class="col-md-12">
                            <x-forms.input name="email" label="E-mail" type="email" required :value="old('email', $person->email)" />
                        </div>

                        <div class="col-md-12">
                            <x-forms.textarea rows="2" name="address" label="Endereço" :value="old('address', $person->address)" />
                        </div>
                    </div>

                    <x-forms.section title="Alterar Senha" />
                    
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Adicionamos o autocomplete="new-password" --}}
                            <x-forms.input 
                                name="password" 
                                label="Nova Senha" 
                                type="password" 
                                autocomplete="new-password" 
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input 
                                name="password_confirmation" 
                                label="Confirmar Nova Senha" 
                                type="password" 
                                autocomplete="new-password" 
                            />
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 mt-4">
                        <x-buttons.submit-button type="submit" class="btn-action new submit">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </x-buttons.submit-button>
                    </div>
                </div>
            </div>
        </x-forms.form-card>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/components/photos.js'])
@endpush