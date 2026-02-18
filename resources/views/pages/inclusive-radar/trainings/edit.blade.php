@extends('layouts.master')

@section('title', "Editar - $training->title")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Treinamentos' => route('inclusive-radar.trainings.index'),
            $training->title => route('inclusive-radar.trainings.show', $training),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Treinamento</h2>
            <p class="text-muted">Atualizando informações de: <strong>{{ $training->title }}</strong></p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card
            action="{{ route('inclusive-radar.trainings.update', $training->id) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            {{-- SEÇÃO 1: Vínculo Polimórfico (Corrigido para MorphMap) --}}
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
                    :selected="old('trainable_type', $training->trainable_type)"
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

            {{-- SEÇÃO 2: Informações do Treinamento --}}
            <x-forms.section title="Informações do Treinamento" />

            <div class="col-md-12">
                <x-forms.input
                    name="title"
                    label="Título do Treinamento *"
                    required
                    :value="old('title', $training->title)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição"
                    rows="3"
                    :value="old('description', $training->description)"
                />
            </div>

            {{-- SEÇÃO 3: Conteúdo e Arquivos --}}
            <x-forms.section title="Conteúdo Didático" />

            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold text-purple-dark">Links / URLs (Vídeos, Referências)</label>
                <div id="url-container">
                    @php
                        $savedUrls = is_array($training->url) ? $training->url : ( $training->url ? [$training->url] : [] );
                        $oldUrls = old('url', (count($savedUrls) > 0 ? $savedUrls : ['']));
                    @endphp

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
                    label="Arquivos do Treinamento"
                    :existingFiles="$training->files"
                    :multiple="true"
                    deleteRoute="inclusive-radar.trainings.files.destroy"
                    :training="$training"
                />
            </div>

            <x-forms.section title="Status" />

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativar Treinamento"
                    description="Fica visível para associação com materiais e TAs"
                    :checked="old('is_active', $training->is_active)"
                />
            </div>

            <div class="col-md-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.trainings.index') }}" variant="secondary">
                    <i class="fas fa-arrow-left"></i> Voltar para Listagem
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save mr-2"></i> Salvar Alterações
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        <script>
            // Dados ajustados para os apelidos do MorphMap
            window.trainingData = {
                items: {
                    'assistive_technology': @json($technologies ?? []),
                    'accessible_educational_material': @json($materials ?? [])
                },
                oldId: "{{ old('trainable_id', $training->trainable_id) }}"
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
                    </button>
                `;
                container.appendChild(div);
            }

            document.addEventListener('DOMContentLoaded', function() {
                const typeSelect = document.getElementById('trainable_type');
                const idSelect = document.getElementById('trainable_id');

                if (typeSelect && idSelect) {
                    typeSelect.addEventListener('change', function() {
                        const selectedType = this.value;
                        const items = window.trainingData.items[selectedType] || [];

                        idSelect.innerHTML = '<option value="">Selecione o item...</option>';

                        items.forEach(item => {
                            const option = new Option(item.name, item.id);
                            idSelect.add(option);
                        });

                        // Seta o valor salvo no banco ou o old() da validação
                        if (window.trainingData.oldId) {
                            idSelect.value = window.trainingData.oldId;
                        }
                    });

                    // Carregamento inicial
                    if (typeSelect.value) {
                        typeSelect.dispatchEvent(new Event('change'));
                    }
                }
            });
        </script>
    @endpush
@endsection
