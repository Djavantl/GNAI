@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => route('specialized-educational-support.teachers.index'),
            'Permissões Globais' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Permissões Globais de Professores</h2>
            <p class="text-muted">As permissões selecionadas aqui serão aplicadas a <strong>todos</strong> os usuários vinculados como professores.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.index') }}" variant="secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        {{-- Rota de update que criamos no Controller --}}
        <x-forms.form-card action="{{ route('specialized-educational-support.teachers.permissions.update') }}" method="POST">
            @method('PUT')
            
            <x-forms.section title="Matriz de Acesso do Docente" />

            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Nota:</strong> Alterações nesta tela impactam imediatamente o acesso de todos os professores cadastrados no sistema.
                </div>

                @foreach($permissions as $group => $groupPermissions)
                    <div class="border-bottom rounded mb-4 overflow-hidden shadow-sm">
                        
                        {{-- Cabeçalho do Módulo --}}
                        <div class="d-flex justify-content-between align-items-center px-4 py-3"
                            style="background-color: #e1e5f1;">
                            <h6 class="mb-0 fw-bold text-purple-dark text-capitalize">
                                <i class="fas fa-folder me-2"></i>
                                {{ ucfirst(str_replace(['-', '_'], ' ', $group)) }}
                            </h6>

                            <x-forms.checkbox
                                name="check_all_{{ $group }}"
                                id="check-all-{{ $group }}"
                                label="Selecionar Todas"
                                class="check-all"
                                data-group="{{ $group }}"
                            />
                        </div>

                        {{-- Permissões do módulo --}}
                        <div class="px-4 py-3 bg-white">
                            <div class="row">
                                @foreach($groupPermissions as $permission)
                                    <div class="col-md-3 mb-3">
                                        <x-forms.checkbox
                                            name="permissions[]"
                                            :value="$permission->id"
                                            :id="'permission-'.$permission->id"
                                            class="permission-checkbox"
                                            data-group="{{ $group }}"
                                            :checked="in_array(
                                                $permission->id,
                                                old('permissions', $globalPermissionsIds ?? [])
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

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 bg-light">
                <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.index') }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-shield-alt"></i> Atualizar Permissões de Professores
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('change', function (e) {
            // Lógica de Selecionar Todas dentro do grupo
            if (e.target.type === 'checkbox') {
                let wrapper = e.target.closest('.custom-checkbox-wrapper');
                if (!wrapper) return;
                
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