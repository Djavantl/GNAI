@extends('layouts.master')

@section('title', "Inspeção - {$inspection->inspection_date->format('d/m/Y')}")

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Materiais Pedagógicos' => route('inclusive-radar.accessible-educational-materials.index'),
            $material->name => route('inclusive-radar.accessible-educational-materials.show', $material),
            'Detalhes da Inspeção' => null
        ]" />
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between mb-3 align-items-md-center gap-3">
        <header>
            <h2 class="text-title">Detalhes da Inspeção</h2>
            <p class="text-muted mb-0">
                Visualize o estado de conservação, tipo de inspeção, parecer técnico e evidências.
            </p>
        </header>

        <div class="text-end ms-md-auto">
            <span class="d-block text-muted small text-uppercase fw-bold mb-1">Data da Inspeção</span>
            <span class="badge bg-purple fs-6 px-3">{{ $inspection->inspection_date->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="mt-3">
        <main class="custom-table-card bg-white shadow-sm">

            <x-forms.section title="Informações Gerais" />

            <div class="row g-3 px-4 pb-4">
                <x-show.info-item
                    label="Estado de Conservação"
                    column="col-md-6"
                    isBox="true"
                >
                    {{ $inspection->state?->label() ?? '---' }}
                </x-show.info-item>

                <x-show.info-item label="Tipo de Inspeção" column="col-md-6" isBox="true">
                    {{ $inspection->type?->label() ?? '---' }}
                </x-show.info-item>

                <x-show.info-textarea label="Parecer Técnico / Descrição" column="col-12" :value="$inspection->description ?: 'Nenhum parecer técnico registrado.'" :rich="true"/>
            </div>

            <x-forms.section title="Evidências" />

            <div class="row g-3 px-4 pb-4">
                @forelse($inspection->evidences as $index => $img)
                    <div class="col-12 col-md-4">
                        <x-forms.evidence-card :evidence="$img" :priority="$loop->first" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5 bg-light rounded border border-dashed">
                            <i class="fas fa-paperclip fa-2x text-secondary mb-2" aria-hidden="true"></i>
                            <p class="text-muted mb-0 small">Nenhuma evidência registrada para esta inspeção.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <footer class="col-12 border-top p-4 d-flex justify-content-end align-items-center bg-light-subtle">
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <x-buttons.link-button :href="route('inclusive-radar.accessible-educational-materials.show', $material)" variant="secondary">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>
        </main>
    </div>
@endsection
