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
            <p class="text-muted mb-1">
                Rastreabilidade de:
                <strong>{{ $assistiveTechnology->name }}</strong>
            </p>

            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small text-uppercase fw-bold">
                    Registros
                </span>
                <span class="badge bg-purple fs-6">
                    {{ $logs->total() }}
                </span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <x-buttons.link-button
                href="{{ route('inclusive-radar.assistive-technologies.show', $assistiveTechnology) }}"
                variant="secondary"
            >
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>
    <x-logs.container :logs="$logs" />

@endsection
