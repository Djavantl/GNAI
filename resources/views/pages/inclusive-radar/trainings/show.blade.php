@extends('layouts.master')

@section('title', $training->title)

@section('content')
    {{-- Cabeçalho --}}
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Treinamentos' => route('inclusive-radar.trainings.index'),
            $training->title => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes do Treinamento</h2>
            <p class="text-muted">Visualize informações do treinamento, conteúdos didáticos e o recurso vinculado.</p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">Status Atual</span>
            <span class="badge bg-{{ $training->is_active ? 'success' : 'secondary' }} fs-6">
                {{ $training->is_active ? 'Ativo' : 'Inativo' }}
            </span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

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

                            <span class="badge bg-purple-light text-purple-dark px-3">
                                {{ $isTA ? 'Tecnologia Assistiva' : 'Material Pedagógico' }}
                            </span>

                            <i class="fas fa-arrow-right text-muted small"></i>

                            <a href="{{ $route }}" class="fw-bold text-primary text-decoration-none">
                                <i class="fas {{ $isTA ? 'fa-laptop-medical' : 'fa-book-reader' }} me-1"></i>
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
                    {!! nl2br(e($training->description)) ?? '---' !!}
                </x-show.info-item>
            </div>

            {{-- SEÇÃO 3: Conteúdo Didático --}}
            <x-forms.section title="Conteúdo Didático" />
            <div class="row g-3 px-4 pb-4">
                {{-- Links / URLs --}}
                <x-show.info-item label="Links e Vídeos de Referência" column="col-md-12" isBox="true">
                    @if($training->url && count($training->url) > 0 && !empty($training->url[0]))
                        <div class="row g-3">
                            @foreach($training->url as $link)
                                @if(!empty($link))
                                    <div class="col-md-6">
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
                                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
                                            </div>
                                        @endif

                                        <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                                            <a href="{{ $link }}" target="_blank" class="text-decoration-none text-truncate pe-2">
                                                <i class="fas fa-external-link-alt me-2 text-primary"></i>
                                                {{ $link }}
                                            </a>
                                            <i class="fas fa-link text-muted small"></i>
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
                <x-show.info-item label="Arquivos do Treinamento (PDF, DOC...)" column="col-md-12" isBox="true">
                    @if($training->files->count())
                        <div class="existing-files-container d-flex flex-column gap-2 w-100">
                            @foreach($training->files as $file)
                                <div class="d-flex gap-2 align-items-center w-100">
                                    <div class="flex-grow-1">
                                        <div class="form-control bg-light d-flex align-items-center justify-content-between">
                                            <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="text-decoration-none text-primary text-truncate">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i>
                                                {{ $file->original_name ?? basename($file->path) }}
                                            </a>
                                            <small class="text-muted ms-2 d-none d-md-inline">
                                                @if(Storage::disk('public')->exists($file->path))
                                                    ({{ number_format(Storage::disk('public')->size($file->path) / 1024, 2) }} KB)
                                                @else
                                                    <span class="text-danger small">(Arquivo não encontrado)</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $file->path) }}" download="{{ $file->original_name }}" class="btn btn-primary" title="Baixar">
                                        <i class="fas fa-download"></i>
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
            <x-forms.section title="Status" />
            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Disponibilidade do Treinamento" column="col-md-12" isBox="true">
                    {{ $training->is_active ? 'Ativo' : 'Inativo - Oculto no sistema' }}
                </x-show.info-item>
            </div>

            {{-- Rodapé de Ações --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small">
                    <i class="fas fa-fingerprint me-1"></i> ID: #{{ $training->id }}
                </div>

                <div class="d-flex gap-2">
                    <x-buttons.link-button :href="route('inclusive-radar.trainings.index')" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>

                    <form action="{{ route('inclusive-radar.trainings.destroy', $training) }}" method="POST" onsubmit="return confirm('Deseja excluir este treinamento?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>

                    <x-buttons.link-button :href="route('inclusive-radar.trainings.edit', $training)" variant="warning">
                        <i class="fas fa-edit"></i> Editar
                    </x-buttons.link-button>
                </div>
            </div>
        </div>
    </div>
@endsection
