@extends('layouts.master')

@section('title', "Inspeção - {$inspection->inspection_date->format('d/m/Y')}")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Barreiras' => route('inclusive-radar.barriers.index'),
        $barrier->name => route('inclusive-radar.barriers.show', $barrier),
        'Inspeção' => null
    ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Detalhes da Inspeção</h2>
            <p class="text-muted">
                Visualize o estado de conservação, tipo de inspeção, parecer técnico e evidências visuais.
            </p>
        </div>
        <div class="text-end">
            <span class="d-block text-muted small uppercase fw-bold">Data da Inspeção</span>
            <span class="badge bg-purple fs-6">{{ $inspection->inspection_date->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="mt-3">
        <div class="custom-table-card bg-white shadow-sm">

            {{-- SEÇÃO 1: Estado de Conservação / Status / Tipo de Inspeção --}}
            <x-forms.section title="Detalhes da Inspeção" />
            <div class="row g-3 px-4 pb-4">
                {{-- Status da Barreira --}}
                <x-show.info-item label="Status da Barreira" column="col-md-6" isBox="true">
                <span class="fw-bold {{ $inspection->status?->color() }}">
                    {{ $inspection->status?->label() ?? 'Identificada' }}
                </span>
                </x-show.info-item>

                {{-- Tipo de Inspeção --}}
                <x-show.info-item label="Tipo de Inspeção" column="col-md-6" isBox="true">
                    {{ $inspection->type?->label() ?? '---' }}
                </x-show.info-item>

                {{-- Parecer Técnico --}}
                @if($inspection->description)
                    <x-show.info-item label="Parecer Técnico" column="col-12" isBox="true">
                        {{ $inspection->description }}
                    </x-show.info-item>
                @endif
            </div>

            {{-- SEÇÃO 2: Evidências Visuais --}}
            <x-forms.section title="Evidências Visuais" />
            <div class="row g-3 px-4 pb-4">
                @if($inspection->images && $inspection->images->count() > 0)
                    @foreach($inspection->images as $img)
                        <div class="col-12 col-md-6">
                            <a href="{{ asset('storage/' . $img->path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $img->path) }}"
                                     class="rounded shadow-sm w-100"
                                     style="height: 250px; object-fit: cover; cursor:pointer;">
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 bg-light rounded border border-dashed w-100">
                        <span class="text-muted small">Nenhuma evidência visual registrada</span>
                    </div>
                @endif
            </div>

            {{-- RODAPÉ DE AÇÕES --}}
            <div class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small">
                    <i class="fas fa-id-card me-1"></i> ID da Inspeção: #{{ $inspection->id }}
                </div>

                <div class="d-flex gap-2">
                    <x-buttons.link-button
                        :href="route('inclusive-radar.barriers.show', $barrier)"
                        variant="secondary">
                        <i class="fas fa-arrow-left me-1"></i> Voltar ao Histórico
                    </x-buttons.link-button>
                </div>
            </div>

        </div> {{-- FIM DO CUSTOM TABLE CARD --}}
    </div>
@endsection
