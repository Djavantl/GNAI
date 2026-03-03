@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cargos' => route('specialized-educational-support.positions.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Novo Cargo</h2>
            <p class="text-muted">Defina as atribuições e o status do cargo no sistema.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.positions.index') }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.positions.store') }}" method="POST">
            
            <x-forms.section title="Informações do Cargo" />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome do Cargo " 
                    required 
                    placeholder="Ex: Professor AEE, Psicólogo..."
                    :value="old('name')" 
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição" 
                    rows="3" 
                    placeholder="Breve descrição das responsabilidades..."
                    :value="old('description')" 
                />
            </div>

            <x-forms.section title="Permissões do Cargo" />
            <div class="col-12 ">
                @foreach($permissions as $group => $groupPermissions)
                    <div class="border-bottom">
                        {{-- Cabeçalho do Grupo --}}
                        <div class="d-flex justify-content-between align-items-center px-4 py-3"
                            style="background-color: #e1e5f1;">
                            <h6 class="mb-0 fw-bold text-purple-dark text-capitalize">
                                {{ ucfirst(str_replace('-', ' ', $group)) }}
                            </h6>
                            <x-forms.checkbox
                                name="check_all_{{ $group }}"
                                id="check-all-{{ $group }}"
                                label="Selecionar Todas"
                                class="check-all"
                                data-group="{{ $group }}"
                            />
                        </div>
                        {{-- Corpo das permissões --}}
                        <div class="px-4 py-3">
                            <div class="row">
                                @foreach($groupPermissions as $permission)
                                    <div class="col-md-3 mb-2">

                                        <x-forms.checkbox
                                            name="permissions[]"
                                            :value="$permission->id"
                                            :id="'permission-'.$permission->id"
                                            class="permission-checkbox"
                                            data-group="{{ $group }}"
                                            :checked="in_array(
                                                $permission->id,
                                                old('permissions',
                                                    isset($position)
                                                        ? $position->permissions->pluck('id')->toArray()
                                                        : []
                                                )
                                            )"
                                            :label="$permission->name"
                                        />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.positions.index') }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('change', function (e) {
            // Se clicou em um checkbox
            if (e.target.type === 'checkbox') {
                let wrapper = e.target.closest('.custom-checkbox-wrapper');
                if (!wrapper) return;
                // Se for um "Selecionar Todas"
                if (wrapper.classList.contains('check-all')) {
                    let group = wrapper.dataset.group;
                    let checked = e.target.checked;
                    if (!group) return;
                    document.querySelectorAll(
                        '.permission-checkbox[data-group="'+group+'"] input'
                    ).forEach(function (checkbox) {
                        checkbox.checked = checked;
                    });
                }

            }

        });

    });
    </script>
    @endpush

@endsection