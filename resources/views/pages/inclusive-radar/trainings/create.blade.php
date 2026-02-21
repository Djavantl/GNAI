@extends('layouts.master')

@section('title', 'Cadastrar - Treinamento')

@section('content')
    <div class="mb-5">
        {{-- Breadcrumb com rótulo de navegação para acessibilidade --}}
        <nav aria-label="Breadcrumb">
            <x-breadcrumb :items="[
                'Home' => route('dashboard'),
                'Treinamentos' => route('inclusive-radar.trainings.index'),
                'Cadastrar' => null
            ]" />
        </nav>
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h1 class="text-title h2">Novo Treinamento</h1>
            <p class="text-muted mb-0">Cadastre treinamentos e capacitações relacionadas às tecnologias e materiais educativos.</p>
        </header>
        <div>
            <x-buttons.link-button
                href="{{ route('inclusive-radar.trainings.index') }}"
                variant="secondary"
                label="Cancelar cadastro e voltar para a lista de treinamentos"
            >
                <i class="fas fa-times" aria-hidden="true"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
            <p class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-2" aria-hidden="true"></i> <strong>Atenção:</strong> Existem erros no preenchimento.</p>
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

            {{-- Uso de seções com títulos claros ajuda na navegação por landmarks --}}
            <x-forms.section title="Vínculo com Recurso" />

            <div class="col-md-6">
                <x-forms.select
                    name="trainable_type"
                    id="trainable_type"
                    label="Tipo de Recurso *"
                    required
                    aria-required="true"
                    aria-description="Selecione se o treinamento é para uma Tecnologia Assistiva ou Material Pedagógico"
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
                    aria-required="true"
                    aria-live="polite"
                    :options="['' => 'Selecione o tipo primeiro']"
                />
            </div>

            <x-forms.section title="Informações do Treinamento" />

            <div class="col-md-12">
                <x-forms.input
                    name="title"
                    label="Título do Treinamento *"
                    required
                    aria-required="true"
                    placeholder="Ex: Capacitação de Software de Acessibilidade"
                    :value="old('title')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição Detalhada"
                    rows="3"
                    placeholder="Detalhes sobre os objetivos e conteúdo do treinamento"
                    :value="old('description')"
                />
            </div>

            <x-forms.section title="Conteúdo Didático" />

            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold text-purple-dark" id="url-label">Links e Referências Externas</label>
                <div id="url-container" role="group" aria-labelledby="url-label">
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
                                    aria-label="URL do recurso {{ $index + 1 }}"
                                >
                                @error("url.$index")
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                            @if($loop->first)
                                <button type="button" class="btn btn-outline-primary" onclick="addUrlField()" title="Adicionar novo campo de link" aria-label="Adicionar outro link">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()" title="Remover este link" aria-label="Remover este link">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-12">
                <x-forms.file-uploader
                    name="files"
                    label="Arquivos de Apoio (Manuais, Apostilas, PDF...)"
                    :multiple="true"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.zip"
                    aria-description="Você pode selecionar múltiplos arquivos para upload"
                />
            </div>

            <x-forms.section title="Configurações de Visibilidade" />

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativar Treinamento"
                    description="Fica visível para associação com materiais e tecnologias"
                    :checked="old('is_active', true)"
                />
            </div>

            {{-- Ações com espaçamento consistente com TA --}}
            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button href="{{ route('inclusive-radar.trainings.index') }}" variant="secondary" label="Voltar para a listagem">
                    <i class="fas fa-arrow-left me-1" aria-hidden="true"></i> Voltar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit" aria-label="Salvar novo treinamento">
                    <i class="fas fa-save me-1" aria-hidden="true"></i> Cadastrar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        <script>
            // Mantendo a lógica funcional mas garantindo que novos campos herdem acessibilidade
            window.trainingData = {
                items: {
                    'assistive_technology': @json($technologies ?? []),
                    'accessible_educational_material': @json($materials ?? [])
                },
                targetId: "{{ $preSelectedId ?? old('trainable_id') }}"
            };

            function addUrlField() {
                const container = document.getElementById('url-container');
                const index = container.querySelectorAll('.url-item').length;
                const div = document.createElement('div');
                div.className = 'd-flex gap-2 mb-2 url-item';
                div.innerHTML = `
                    <div class="flex-grow-1">
                        <input type="url" name="url[]" class="form-control" placeholder="Ex: https://..." aria-label="URL do recurso ${index + 1}">
                    </div>
                    <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()" aria-label="Remover este link">
                        <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>`;
                container.appendChild(div);
                div.querySelector('input').focus(); // Foca no novo campo para facilitar a navegação por teclado
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
