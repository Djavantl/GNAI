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
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Dados injetados com segurança para o Dashboard.js
        window.dashboardData = {
            students: {{ $totalStudents ?? 0 }},
            professionals: {{ $totalProfessionals ?? 0 }},
            peiTotal: {{ $totalPeis ?? 0 }},
            peiFinished: {{ $totalPeisFinished ?? 0 }},
            peiNotFinished: {{ $totalPeisNotFinished ?? 0 }},
            colors: {
                primary: '#4D44B5',
                secondary: '#6c63ff',
                success: '#28c76f',
                warning: '#ff9f43'
            }
        };
    </script>
    @vite('resources/js/pages/dashboard.js')
@endpush