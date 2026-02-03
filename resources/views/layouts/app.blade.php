@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="bi bi-house-door"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
    
    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-custom stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-0">Alunos</h6>
                            <h3 class="text-white mb-0">42</h3>
                        </div>
                        <div class="bg-white p-3 rounded-circle">
                            <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card card-custom stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-0">Sessões Hoje</h6>
                            <h3 class="text-white mb-0">8</h3>
                        </div>
                        <div class="bg-white p-3 rounded-circle">
                            <i class="bi bi-calendar-check text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card card-custom stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-0">Pendências</h6>
                            <h3 class="text-white mb-0">5</h3>
                        </div>
                        <div class="bg-white p-3 rounded-circle">
                            <i class="bi bi-exclamation-triangle text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card card-custom stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-0">Empréstimos</h6>
                            <h3 class="text-white mb-0">12</h3>
                        </div>
                        <div class="bg-white p-3 rounded-circle">
                            <i class="bi bi-arrow-left-right text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Conteúdo Principal -->
    <div class="row">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Visão Geral</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Bem-vindo ao sistema de gestão educacional.</p>
                    <p>Use o menu lateral para acessar as diferentes funcionalidades do sistema.</p>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Dica:</strong> A sidebar é responsiva e se adapta a diferentes tamanhos de tela.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Atalhos Rápidos</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-plus-circle text-primary me-2"></i>
                            Novo Aluno
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-calendar-plus text-primary me-2"></i>
                            Agendar Sessão
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                            Gerar Relatório
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-bell text-primary me-2"></i>
                            Ver Pendências
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Estilos adicionais específicos para esta página */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
    </style>
@endpush