@extends('layouts.app')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cargos' => route('specialized-educational-support.positions.index'),
            $position->name => route('specialized-educational-support.positions.show', $position),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Cargo</h2>
            <p class="text-muted">Alterando informações do cargo: <strong>{{ $position->name }}</strong></p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.positions.update', $position) }}" method="POST">
            @method('PUT')
            
            <x-forms.section title="Atualizar Dados" />

            <div class="col-md-6">
                <x-forms.input 
                    name="name" 
                    label="Nome do Cargo *" 
                    required 
                    :value="old('name', $position->name)" 
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="is_active"
                    label="Status *"
                    required
                    :options="['1' => 'Ativo', '0' => 'Inativo']"
                    :value="old('is_active', $position->is_active)"
                    :selected="old('is_active', $position->is_active)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea 
                    name="description" 
                    label="Descrição" 
                    rows="3" 
                    :value="old('description', $position->description)" 
                />
            </div>

            <x-forms.section title="Informações do Cargo" />
            
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
        </x-forms.form-card>
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