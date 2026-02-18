@extends('layouts.master')

@section('title', "Histórico - $material->name")

@section('content')
    {{-- 1. Cabeçalho e Breadcrumb --}}
    <div class="mb-4">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos' => route('inclusive-radar.accessible-educational-materials.index'),
            $material->name => route('inclusive-radar.accessible-educational-materials.show', $material),
            'Histórico de Alterações' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Histórico de Alterações</h2>
            <p class="text-muted">Rastreabilidade de: <strong>{{ $material->name }}</strong></p>
        </div>

        <div class="d-flex gap-2">
            @if($material->asset_code)
                <div class="text-end me-3">
                    <span class="d-block text-muted small uppercase fw-bold">Patrimônio</span>
                    <span class="badge bg-purple fs-6">{{ $material->asset_code }}</span>
                </div>
            @endif

            <x-buttons.link-button
                href="{{ route('inclusive-radar.accessible-educational-materials.show', $material) }}"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>

            <x-buttons.pdf-button
                :href="route('inclusive-radar.accessible-educational-materials.logs.pdf', $material)"
                class="ms-3"
            />
        </div>
    </div>

    {{-- O componente container já está preparado para lidar com a coleção $logs --}}
    <x-logs.container :logs="$logs" />

@endsection
