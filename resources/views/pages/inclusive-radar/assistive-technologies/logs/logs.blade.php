@extends('layouts.master')

@section('title', "Histórico - $assistiveTechnology->name")

@section('content')
    <div class="mb-4">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Tecnologias Assistivas' => route('inclusive-radar.assistive-technologies.index'),
            $assistiveTechnology->name => route('inclusive-radar.assistive-technologies.show', $assistiveTechnology),
            'Histórico de Alterações' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Histórico de Alterações</h2>
            <p class="text-muted">Rastreabilidade de: <strong>{{ $assistiveTechnology->name }}</strong></p>
        </div>

        <div class="d-flex gap-2">
            <div class="text-end me-3">
                <span class="d-block text-muted small uppercase fw-bold">Registros</span>
                <span class="badge bg-purple fs-6">{{ $logs->total() }}</span>
            </div>

            <x-buttons.link-button
                href="{{ route('inclusive-radar.assistive-technologies.show', $assistiveTechnology) }}"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
            <x-buttons.pdf-button :href="route('inclusive-radar.assistive-technologies.logs.pdf', $assistiveTechnology)" class="ms-3" />
        </div>
    </div>
    <x-logs.container :logs="$logs" />

@endsection
