@extends('layouts.master')

@section('title', "Inspeção - {$inspection->inspection_date->format('d/m/Y')}")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Barreiras' => route('inclusive-radar.barriers.index'),
            $barrier->name => route('inclusive-radar.barriers.show', $barrier),
            'Detalhes da Inspeção' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h2 class="text-title">Detalhes da Inspeção</h2>
            <p class="text-muted mb-0">
                Visualize o status da barreira, parecer técnico e evidências visuais coletadas.
            </p>
        </header>

        <div class="text-end">
            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Data da Inspeção</span>
            <span class="badge bg-purple fs-6 px-3">{{ $inspection->inspection_date->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="mt-3">
        <main class="custom-table-card bg-white shadow-sm">

            <x-forms.section title="Informações Gerais" />

            <div class="row g-3 px-4 pb-4">
                <x-show.info-item label="Status da Barreira" column="col-md-6" isBox="true">
                    {{ $inspection->status?->label() ?? 'Identificada' }}
                </x-show.info-item>

                <x-show.info-item label="Tipo de Inspeção" column="col-md-6" isBox="true">
                    {{ $inspection->type?->label() ?? '---' }}
                </x-show.info-item>

                <x-show.info-textarea label="Parecer Técnico / Descrição" column="col-12" :value="$inspection->description ?: 'Nenhum parecer técnico registrado.'" :rich="true"/>
            </div>

            <x-forms.section title="Evidências Visuais" />

            <div class="row g-3 px-4 pb-4">
                @forelse($inspection->images as $index => $img)
                    <div class="col-12 col-md-4">
                        <a href="{{ asset('storage/' . $img->path) }}"
                           target="_blank"
                           class="d-block border rounded overflow-hidden shadow-sm"
                           aria-label="Ver evidência visual {{ $index + 1 }} em tamanho real"
                        >
                            <img src="{{ asset('storage/' . $img->path) }}"
                                 class="w-100"
                                 alt="Foto de evidência {{ $index + 1 }}"
                                 width="444"
                                 height="250"
                                 @if($loop->first) fetchpriority="high" loading="eager" @else loading="lazy" @endif
                                 style="height: 250px; object-fit: cover; transition: transform 0.3s;"
                            >
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 bg-light rounded border border-dashed">
                            <i class="fas fa-camera fa-2x text-secondary mb-2" aria-hidden="true"></i>
                            <p class="text-muted mb-0 small">Nenhuma evidência visual registrada para esta inspeção.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small">
                    <i class="fas fa-fingerprint me-1" aria-hidden="true"></i> ID da Inspeção: #{{ $inspection->id }}
                </div>

                <div class="d-flex gap-2">
                    <x-buttons.link-button :href="route('inclusive-radar.barriers.show', $barrier)" variant="secondary">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
