@extends('layouts.master')

@section('title', $training->title)

@section('content')
    {{-- Cabeçalho com Navegação Acessível --}}
    <div class="mb-5">
        <nav aria-label="Breadcrumb">
            <x-breadcrumb :items="[
                'Home' => route('dashboard'),
                'Treinamentos' => route('inclusive-radar.trainings.index'),
                $training->title => null
            ]" />
        </nav>
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h1 class="text-title h2">Detalhes do Treinamento</h1>
            <p class="text-muted mb-0">Visualize informações do treinamento, conteúdos didáticos e o recurso vinculado.</p>
        </header>
        <div role="group" aria-label="Ações principais">
            <x-buttons.link-button
                :href="route('inclusive-radar.trainings.edit', $training)"
                variant="warning"
                label="Editar informações deste treinamento"
            >
                <i class="fas fa-edit" aria-hidden="true"></i> Editar
            </x-buttons.link-button>
            <x-buttons.link-button
                href="{{ route('inclusive-radar.trainings.index') }}"
                variant="secondary"
                label="Voltar para a lista de treinamentos"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="mt-3">
        <main class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Vínculo com Recurso --}}
            <x-forms.section title="Vínculo com Recurso" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Tipo de Recurso / Item Específico" column="col-md-12" isBox="true">
                    @if($training->trainable)
                        <div class="d-flex align-items-center gap-2">
                            @php
                                $isTA = $training->trainable_type === 'assistive_technology';
                                $route = $isTA
                                    ? route('inclusive-radar.assistive-technologies.show', $training->trainable_id)
                                    : route('inclusive-radar.accessible-educational-materials.show', $training->trainable_id);
                            @endphp

                            <span class="badge bg-purple-light text-purple-dark px-3" role="status">
                                {{ $isTA ? 'Tecnologia Assistiva' : 'Material Pedagógico' }}
                            </span>

                            <i class="fas fa-arrow-right text-muted small" aria-hidden="true"></i>

                            <a href="{{ $route }}" class="fw-bold text-primary text-decoration-none" aria-label="Ver detalhes do recurso: {{ $training->trainable->name }}">
                                <i class="fas {{ $isTA ? 'fa-laptop-medical' : 'fa-book-reader' }} me-1" aria-hidden="true"></i>
                                {{ $training->trainable->name }}
                            </a>
                        </div>
                    @else
                        <span class="text-muted italic">Nenhum recurso vinculado.</span>
                    @endif
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 2: Informações do Treinamento --}}
            <x-forms.section title="Informações do Treinamento" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Título do Treinamento" column="col-md-12" isBox="true">
                    <strong>{{ $training->title }}</strong>
                </x-show.info-item>

                <x-show.info-item label="Descrição" column="col-md-12" isBox="true">
                    <div class="text-dark">
                        {!! nl2br(e($training->description)) ?: '<span class="text-muted">Nenhuma descrição fornecida.</span>' !!}
                    </div>
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 3: Conteúdo Didático --}}
            <x-forms.section title="Conteúdo Didático" />
            <div class="row g-3 px-4 pb-4">
                {{-- Links / URLs --}}
                <x-show.info-item label="Links e Referências Externas" column="col-md-12" isBox="true">
                    @if($training->url && count($training->url) > 0 && !empty($training->url[0]))
                        <div class="row g-3" role="list">
                            @foreach($training->url as $link)
                                @if(!empty($link))
                                    <div class="col-md-6" role="listitem">
                                        @php
                                            $videoId = null;
                                            if (Str::contains($link, ['youtube.com', 'youtu.be'])) {
                                                if (Str::contains($link, 'v=')) {
                                                    parse_str(parse_url($link, PHP_URL_QUERY), $query);
                                                    $videoId = $query['v'] ?? null;
                                                } else {
                                                    $videoId = basename(parse_url($link, PHP_URL_PATH));
                                                }
                                            }
                                        @endphp

                                        @if($videoId)
                                            <div class="ratio ratio-16x9 mb-2 shadow-sm rounded overflow-hidden border">
                                                <iframe
                                                    src="https://www.youtube.com/embed/{{ $videoId }}"
                                                    title="Vídeo de treinamento: {{ $training->title }}"
                                                    allowfullscreen>
                                                </iframe>
                                            </div>
                                        @endif

                                        <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                                            <a href="{{ $link }}" target="_blank" class="text-decoration-none text-truncate pe-2" title="Abrir link em nova aba">
                                                <i class="fas fa-external-link-alt me-2 text-primary" aria-hidden="true"></i>
                                                {{ $link }}
                                            </a>
                                            <i class="fas fa-link text-muted small" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">Nenhum link cadastrado.</span>
                    @endif
                </x-show.info-item>

                {{-- Arquivos --}}
                <x-show.info-item label="Arquivos do Treinamento (Manuais, Apostilas, PDF...)" column="col-md-12" isBox="true">
                    @if($training->files->count())
                        <div class="existing-files-container d-flex flex-column gap-2 w-100" role="list">
                            @foreach($training->files as $file)
                                <div class="d-flex gap-2 align-items-center w-100" role="listitem">
                                    <div class="flex-grow-1">
                                        <div class="form-control bg-light d-flex align-items-center justify-content-between">
                                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="text-decoration-none text-primary text-truncate" title="Visualizar arquivo">
                                                <i class="fas fa-file-pdf me-2 text-danger" aria-hidden="true"></i>
                                                {{ $file->original_name ?? basename($file->path) }}
                                            </a>
                                            <small class="text-muted ms-2 d-none d-md-inline">
                                                @if(Storage::disk('public')->exists($file->path))
                                                    ({{ number_format(Storage::disk('public')->size($file->path) / 1024, 2) }} KB)
                                                @else
                                                    <span class="text-danger small">(Arquivo não disponível)</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $file->path) }}" download="{{ $file->original_name }}" class="btn btn-primary shadow-sm" aria-label="Baixar arquivo: {{ $file->original_name }}">
                                        <i class="fas fa-download" aria-hidden="true"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">Nenhum arquivo anexado.</span>
                    @endif
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 4: Status --}}
            <x-forms.section title="Configurações de Visibilidade" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Status no Sistema" column="col-md-12" isBox="true">
                    <span class="text-{{ $training->is_active ? 'success' : 'secondary' }} fw-bold text-uppercase" role="status">
                        {{ $training->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações (Igual ao da TA) --}}
            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID do Sistema: #{{ $training->id }}
                    <x-buttons.pdf-button :href="route('inclusive-radar.trainings.pdf', $training)" class="ms-1" />
                    <x-buttons.excel-button :href="route('inclusive-radar.trainings.excel', $training)" class="ms-1" />
                </div>

                <div class="d-flex gap-2" role="group" aria-label="Ações de gestão do treinamento">
                    <form action="{{ route('inclusive-radar.trainings.destroy', $training) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente este treinamento?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger" label="Excluir este treinamento">
                            <i class="fas fa-trash-alt" aria-hidden="true"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button
                        href="{{ route('inclusive-radar.trainings.index') }}"
                        variant="secondary"
                        label="Voltar para a lista de treinamentos"
                    >
                        <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
