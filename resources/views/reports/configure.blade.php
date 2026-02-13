@extends('layouts.master')

@section('title', 'Relatórios - Filtros')

@section('content')
    <style>
        .custom-table-card.overflow-hidden {
            overflow: visible !important;
        }

        body {
            overflow: visible !important;
        }

        .sticky-action-footer {
            position: -webkit-sticky;
            position: sticky;
            bottom: 0;
            z-index: 1030;
            background: #ffffff;
            border-top: 2px solid #6f42c1;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 -10px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
        }

        .footer-results-active {
            border-top-color: #6f42c1;
        }
    </style>

    @php
        $hasResults = collect($data ?? [])->contains(fn($module) => count($module) > 0);

        $checkActive = function($prefix) {
            return collect(request()->all())->filter(function($value, $key) use ($prefix) {
                return str_starts_with($key, $prefix) && !empty($value);
            })->isNotEmpty();
        };

        $taActive = $checkActive('ta_');
        $matActive = $checkActive('mat_');
    @endphp

    <div class="mb-4">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Relatórios' => route('report.reports.index'),
            'Configurar Filtros' => '#'
        ]"/>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title m-0">Refinar Relatório</h2>
            <p class="text-muted small">Módulos ativos:
                @if(request('ta'))
                    <span class="badge bg-primary">Tecnologias Assistivas</span>
                @endif
                @if(request('materials'))
                    <span class="badge bg-success">Materiais Pedagógicos</span>
                @endif
                @if(request('students'))
                    <span class="badge bg-info">Alunos</span>
                @endif
            </p>
        </div>
        <x-buttons.link-button href="{{ route('report.reports.index') }}" variant="secondary" class="btn-sm">
            Alterar Módulos
        </x-buttons.link-button>
    </div>

    <x-forms.form-card method="GET" action="{{ route('report.reports.configure') }}">
        @foreach(request()->only(['ta', 'materials', 'students']) as $key => $val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endforeach

        <div class="col-12 p-0">
            <div class="accordion border-0" id="reportFilters">

                {{-- Módulo TA --}}
                @if(request('ta'))
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="heading-ta">
                            <button
                                class="accordion-button bg-light text-primary fw-bold {{ $taActive ? '' : 'collapsed' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#f-ta"
                                aria-expanded="{{ $taActive ? 'true' : 'false' }}"
                                aria-controls="f-ta">
                                1. Filtros: Tecnologias Assistivas
                            </button>
                        </h2>
                        <div id="f-ta"
                             class="accordion-collapse collapse {{ $taActive ? 'show' : '' }}"
                             aria-labelledby="heading-ta"
                             data-bs-parent="#reportFilters">
                            <div class="accordion-body p-0">
                                <div class="row g-0">
                                    {{-- Passa items, mas não step --}}
                                    @include('reports.filters.inclusive-radar.assistive-technologies', [
                                        'items' => $data['ta'] ?? []
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Módulo Materiais --}}
                @if(request('materials'))
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="heading-mpa">
                            <button
                                class="accordion-button bg-light text-success fw-bold {{ $matActive ? '' : 'collapsed' }}"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#f-mpa"
                                aria-expanded="{{ $matActive ? 'true' : 'false' }}"
                                aria-controls="f-mpa">
                                {{ request('ta') ? '2' : '1' }}. Filtros: Materiais Pedagógicos
                            </button>
                        </h2>
                        <div id="f-mpa"
                             class="accordion-collapse collapse {{ $matActive ? 'show' : '' }}"
                             aria-labelledby="heading-mpa"
                             data-bs-parent="#reportFilters">
                            <div class="accordion-body p-0">
                                <div class="row g-0">
                                    @include('reports.filters.inclusive-radar.accessible-educational-materials', [
                                        'items' => $data['materials'] ?? []
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div
            class="col-12 sticky-action-footer d-flex flex-wrap justify-content-between align-items-center {{ $hasResults ? 'footer-results-active' : '' }}">
            <div class="d-flex align-items-center gap-2">
                @if($hasResults)
                    <span class="text-success small fw-bold d-none d-md-inline me-2">
                        Exportar:
                    </span>
                    <x-buttons.link-button href="{!! route('report.reports.exportPdf', request()->all()) !!}"
                                           class="btn btn-danger btn-sm px-3">
                        PDF
                    </x-buttons.link-button>
                    <x-buttons.link-button href="{!! route('report.reports.exportExcel', request()->all()) !!}"
                                           variant="success" class="btn-sm px-3">
                        Excel
                    </x-buttons.link-button>
                @else
                    <span class="text-muted small">
                        Defina os filtros para gerar o relatório
                    </span>
                @endif
            </div>

            <x-buttons.submit-button class="btn-action new submit px-5">
                {{ $hasResults ? 'Atualizar Resultados' : 'Aplicar Filtros' }}
            </x-buttons.submit-button>
        </div>
    </x-forms.form-card>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activeElements = document.querySelectorAll('.accordion-collapse.show');
            activeElements.forEach(function (el) {
                new bootstrap.Collapse(el, {toggle: false}).show();
            });
        });
    </script>
@endsection
