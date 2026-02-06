@extends('layouts.master')

@section('title', 'Painel de Controle')

@section('content')
    <div class="mb-4">
        <h2 class="text-title">Dashboard</h2>
        <p class="text-muted">Bem-vindo(a) ao sistema GNAI, <strong>{{ auth()->user()->person->name ?? auth()->user()->email }}</strong>.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-primary-custom text-white rounded-circle p-3 me-3">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-base">Alunos Atendidos</h6>
                        <h3 class="fw-bold mb-0 text-title" style="color: var(--primary-color) !important;">
                            {{ $totalStudents ?? 0 }}
                        </h3>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 pb-4">
                    <a href="{{ route('specialized-educational-support.students.index') }}" class="text-primary-custom text-decoration-none small fw-bold">
                        Ver todos os alunos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card card-custom h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-info text-white rounded-circle p-3 me-3" style="background-color: #6c63ff !important;">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 text-base">Sessões Agendadas</h6>
                        <h3 class="fw-bold mb-0 text-title" style="color: #6c63ff !important;">
                            {{ $totalSessions ?? 0 }}
                        </h3>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 px-4 pb-4">
                    <a href="{{ route('specialized-educational-support.sessions.index') }}" class="text-decoration-none small fw-bold" style="color: #6c63ff;">
                        Gerenciar agenda <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-4">
            <div class="card card-custom h-100 stat-card">
                <div class="card-body p-4 text-white">
                    <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Gestão de NAIs</h5>
                    <p class="small opacity-75 mb-0">
                        Utilize o menu lateral para gerenciar os planos de ensino (PEI), registros de atendimentos e relatórios técnicos do aluno.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection