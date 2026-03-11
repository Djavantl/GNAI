@extends('layouts.master')

@section('title', 'Painel de Controle')

@section('content')
<div class="page-transition">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="text-title mb-1">Dashboard</h2>
            <p class="text-muted mb-0">
                Bem-vindo(a) ao sistema GNAI, <strong>{{ auth()->user()->person->name ?? auth()->user()->email }}</strong>.
            </p>
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-white text-primary-custom p-2 px-3 shadow-sm" style="border-radius: 10px;">
                <i class="bi bi-calendar3"></i> {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('specialized-educational-support.students.index') }}" class="text-decoration-none h-100">
                <div class="card card-custom border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary-custom text-white rounded-3 p-2 me-3">
                                <i class="bi bi-people"></i> </div>
                            <h6 class="text-muted small mb-0 fw-bold">Alunos</h6>
                        </div>
                        <h3 class="text-title mb-0">{{ $totalStudents ?? 0 }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('specialized-educational-support.professionals.index') }}" class="text-decoration-none h-100">
                <div class="card card-custom border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-3 p-2 me-3 text-white" style="background-color: #6c63ff;">
                                <i class="bi bi-person-badge-fill"></i> </div>
                            <h6 class="text-muted small mb-0 fw-bold">Equipe</h6>
                        </div>
                        <h3 class="text-title mb-0">{{ $totalProfessionals ?? 0 }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('specialized-educational-support.pei.all', ['is_finished' => true]) }}" class="text-decoration-none h-100">
                <div class="card card-custom border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-success rounded-3 p-2 me-3 text-white">
                                <i class="bi bi-file-earmark-check-fill"></i> </div>
                            <h6 class="text-muted small mb-0 fw-bold">PEIs OK</h6>
                        </div>
                        <h3 class="text-title mb-0">{{ $totalPeisFinished ?? 0 }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('specialized-educational-support.sessions.index') }}" class="text-decoration-none h-100">
                <div class="card card-custom border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-warning rounded-3 p-2 me-3 text-white">
                                <i class="bi bi-calendar-check-fill"></i> </div>
                            <h6 class="text-muted small mb-0 fw-bold">Sessões</h6>
                        </div>
                        <h3 class="text-title mb-0">{{ $totalSessions ?? 0 }}</h3>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-custom border-0 shadow-sm p-4 h-100">
                <h5 class="text-title mb-4">Distribuição: Pessoas no Sistema</h5>
                <div style="height: 350px;">
                    <canvas id="barChartPeople"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm p-4 h-100">
                <h5 class="text-title mb-4 text-center">Status dos PEIs</h5>
                <div style="height: 250px;">
                    <canvas id="pieChartPei"></canvas>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted"><i class="bi bi-circle-fill me-1 text-success"></i> Finalizados</span>
                        <span class="fw-bold">{{ $totalPeisFinished ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted"><i class="bi bi-circle-fill me-1 text-warning"></i> Não Finalizados</span>
                        <span class="fw-bold">{{ $totalPeisNotFinished ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted"><i class="bi bi-circle-fill me-1 text-primary"></i> Total Geral</span>
                        <span class="fw-bold">{{ $totalPeis ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEÇÃO RADAR INCLUSIVO: GRÁFICO E MAPA --}}

    {{-- SEÇÃO RADAR INCLUSIVO: CARDS DE RESUMO --}}
    <div class="row g-4 mt-2 mb-4">
        <div class="col-6 col-md-4">
            <a href="{{ route('inclusive-radar.assistive-technologies.index') }}" class="text-decoration-none h-100">
                <div class="card card-custom border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary-custom text-white rounded-3 p-2 me-3">
                                <i class="bi bi-cpu"></i>
                            </div>
                            <h6 class="text-muted small mb-0 fw-bold">Tecnologias Assistivas</h6>
                        </div>
                        <h3 class="text-title mb-0">{{ $totalAt ?? 0 }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-4">
            <a href="{{ route('inclusive-radar.accessible-educational-materials.index') }}" class="text-decoration-none h-100">
                <div class="card card-custom border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-info rounded-3 p-2 me-3 text-white">
                                <i class="bi bi-book"></i>
                            </div>
                            <h6 class="text-muted small mb-0 fw-bold">Materiais Pedagógicos Acessíveis</h6>
                        </div>
                        <h3 class="text-title mb-0">{{ $totalAem ?? 0 }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-4">
            <div class="card card-custom border-0 shadow-sm h-100 position-relative">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <a href="{{ route('inclusive-radar.loans.index') }}" class="text-decoration-none">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success rounded-3 p-2 me-3 text-white">
                                    <i class="bi bi-arrow-left-right"></i>
                                </div>
                                <h6 class="text-muted small mb-0 fw-bold">Empréstimos</h6>
                            </div>
                            <h3 class="text-title mb-0">{{ $totalLoans ?? 0 }}</h3>
                        </a>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('inclusive-radar.waitlists.index') }}"
                           class="d-flex align-items-center justify-content-between p-2 rounded-3 text-decoration-none shadow-none border"
                           style="background-color: rgba(0, 207, 232, 0.05); border-color: rgba(0, 207, 232, 0.3) !important;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hourglass-split text-info me-2"></i>
                                <span class="small fw-bold text-muted">Fila de Espera: Aguardando/Notificados</span>
                            </div>
                            <span class="badge bg-info text-white rounded-pill">{{ $totalWaiting ?? 0 }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        {{-- Gráfico de Pizza --}}
        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm p-4 h-100">
                <h5 class="text-title mb-4 text-center">Status das Barreiras</h5>
                <div style="height: 250px;">
                    <canvas id="doughnutChartBarriers"></canvas>
                </div>
                <div class="mt-4 pt-3 border-top text-center">
                    <span class="text-muted small">Total Identificado: <strong>{{ $totalBarriers ?? 0 }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Mapa com Overlay de Bloqueio e Switches --}}
        <div class="col-lg-8">
            <div class="card card-custom border-0 shadow-sm h-100 d-flex flex-column">

                {{-- Container do Mapa com Overlay Relativo --}}
                <div style="position: relative; flex-grow: 1; min-height: 400px;">
                    <div id="mapDashboard" style="height: 100%; border-radius: 15px 15px 0 0;"></div>

                    {{-- OVERLAY DE BLOQUEIO (Estilo do seu Show) --}}
                    <div id="map-blocked-overlay" class="d-none"
                         style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 1000; display: flex; align-items: center; justify-content: center; border-radius: 15px 15px 0 0; cursor: not-allowed;">
                    <span class="bg-white p-3 rounded shadow-sm border text-center">
                        <i class="fas fa-lock text-danger mb-2 d-block"></i>
                        <span id="blocked-message" class="fw-bold text-muted">
                            Mapa não se aplica aos filtros selecionados.
                        </span>
                    </span>
                    </div>
                </div>

                {{-- Barra de Interruptores (Filtros) --}}
                <div class="bg-light p-3 border-top d-flex flex-wrap justify-content-center gap-3" style="border-radius: 0 0 15px 15px;">
                    <span class="small fw-bold text-muted w-100 text-center mb-1">VISUALIZAR NO MAPA:</span>

                    <div class="form-check form-switch">
                        <input class="form-check-input filter-switch" type="checkbox" id="switch_all" value="all" checked style="cursor: pointer;">
                        <label class="form-check-label small fw-bold" for="switch_all" style="cursor: pointer;">Todas</label>
                    </div>

                    @foreach(App\Enums\InclusiveRadar\BarrierStatus::cases() as $status)
                        <div class="form-check form-switch">
                            <input class="form-check-input filter-switch status-specific" type="checkbox"
                                   id="switch_{{ $status->value }}"
                                   value="{{ $status->value }}" checked style="cursor: pointer;">
                            <label class="form-check-label small text-{{ $status->color() }} fw-bold" for="switch_{{ $status->value }}" style="cursor: pointer;">
                                {{ $status->label() }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Dados injetados com segurança para o Dashboard.js
        window.dashboardData = {
            students: {{ $totalStudents ?? 0 }},
            professionals: {{ $totalProfessionals ?? 0 }},
            peiTotal: {{ $totalPeis ?? 0 }},
            peiFinished: {{ $totalPeisFinished ?? 0 }},
            peiNotFinished: {{ $totalPeisNotFinished ?? 0 }},
            // Dados Radar
            barrierStatuses: @json($barrierStatusCounts ?? []),
            mapBarriers: @json($mapBarriers ?? []),

            colors: {
                primary: '#4D44B5',
                secondary: '#6c63ff',
                success: '#28c76f',
                warning: '#ff9f43',
                info: '#00cfe8',
                danger: '#ea5455',
                muted: '#6c757d'
            }
        };
    </script>
    @vite('resources/js/pages/dashboard.js')
@endpush
