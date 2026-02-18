@extends('layouts.master')

@section('title', 'Cadastrar - Treinamento')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Treinamentos' => route('inclusive-radar.trainings.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Treinamento</h2>
            <p class="text-muted">Cadastre treinamentos e capacitações relacionadas às tecnologias e materiais educativos.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <p class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-2"></i> Atenção: Existem erros no preenchimento.</p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.trainings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-forms.section title="Vínculo com Recurso" />

            <div class="col-md-6">
                <x-forms.select
                    name="trainable_type"
                    id="trainable_type"
                    label="Tipo de Recurso *"
                    required
                    :options="[
                        'assistive_technology' => 'Tecnologia Assistiva',
                        'accessible_educational_material' => 'Material Pedagógico'
                    ]"
                    :selected="$preSelectedType ?? old('trainable_type')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="trainable_id"
                    id="trainable_id"
                    label="Item Específico *"
                    required
                    :options="['' => 'Selecione o tipo primeiro']"
                />
            </div>

            <x-forms.section title="Informações do Treinamento" />

            <div class="col-md-12">
                <x-forms.input
                    name="title"
                    label="Título do Treinamento *"
                    required
                    placeholder="Ex: Capacitação de Software de Acessibilidade"
                    :value="old('title')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição"
                    rows="3"
                    placeholder="Detalhes sobre o treinamento"
                    :value="old('description')"
                />
            </div>

            <x-forms.section title="Conteúdo Didático" />

            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold text-purple-dark">Links / URLs (Vídeos, Referências)</label>
                <div id="url-container">
                    @php $oldUrls = old('url', ['']); @endphp
                    @foreach($oldUrls as $index => $url)
                        <div class="d-flex gap-2 mb-2 url-item">
                            <div class="flex-grow-1">
                                <input
                                    type="url"
                                    name="url[]"
                                    class="form-control @error("url.$index") is-invalid @enderror"
                                    placeholder="Ex: https://youtube.com/watch?v=..."
                                    value="{{ $url }}"
                                >
                                @error("url.$index")
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @if($loop->first)
                                <button type="button" class="btn btn-outline-primary" onclick="addUrlField()" title="Adicionar mais links">
                                    <i class="fas fa-plus"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-12">
                <x-forms.file-uploader
                    name="files"
                    label="Arquivos do Treinamento (PDF, DOC...)"
                    :multiple="true"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.zip"
                />
            </div>

            <x-forms.section title="Status" />

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativar Treinamento"
                    description="Fica visível para associação com materiais e TAs"
                    :checked="old('is_active', true)"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.trainings.index') }}" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Voltar para Listagem
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Finalizar Cadastro
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        <script>
            window.trainingData = {
                items: {
                    // Chaves ajustadas para bater com o MorphMap
                    'assistive_technology': @json($technologies ?? []),
                    'accessible_educational_material': @json($materials ?? [])
                },
                targetId: "{{ $preSelectedId ?? old('trainable_id') }}"
            };

            function addUrlField() {
                const container = document.getElementById('url-container');
                const div = document.createElement('div');
                div.className = 'd-flex gap-2 mb-2 url-item';
                div.innerHTML = `
                    <div class="flex-grow-1">
                        <input type="url" name="url[]" class="form-control" placeholder="Ex: https://...">
                    </div>
                    <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                        <i class="fas fa-trash"></i>
                    </button>`;
                container.appendChild(div);
            }

            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.getElementById('trainable_type');
                const idSelect = document.getElementById('trainable_id');

                function updateIdOptions(selectedType, selectedId = null) {
                    const items = window.trainingData.items[selectedType] || [];
                    idSelect.innerHTML = '<option value="">Selecione o item...</option>';

                    items.forEach(item => {
                        const option = new Option(item.name, item.id);
                        if (selectedId && item.id == selectedId) {
                            option.selected = true;
                        }
                        idSelect.add(option);
                    });
                }

                if (typeSelect && idSelect) {
                    typeSelect.addEventListener('change', function() {
                        updateIdOptions(this.value);
                    });

                    if (typeSelect.value) {
                        updateIdOptions(typeSelect.value, window.trainingData.targetId);
                    }
                }
            });
        </script>
    @endpush
@endsection
