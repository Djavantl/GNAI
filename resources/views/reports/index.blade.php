@extends('layouts.master')

@section('title', 'Relatórios - Seleção')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="['Home' => route('dashboard'), 'Relatórios' => '#']" />
    </div>

    <div class="text-center mb-5">
        <h2 class="text-title">Gerador de Relatórios</h2>
        <p class="text-muted">Escolha quais módulos deseja compor no seu relatório consolidado.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Formulário envia para a mesma rota, mas agora com os parâmetros dos módulos --}}
            <form method="GET" action="{{ route('report.reports.index') }}">
                <div class="row g-4">

                    {{-- Card Tecnologias Assistivas --}}
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 p-4 text-center">
                            <h5 class="fw-bold">Tecnologias</h5>
                            <p class="small text-muted">Recursos e equipamentos de acessibilidade.</p>
                            <div class="form-check form-switch d-flex justify-content-center mt-3">
                                <input class="form-check-input" type="checkbox" name="ta" value="1" id="checkTA" style="transform: scale(1.5)">
                            </div>
                        </div>
                    </div>

                    {{-- Card Materiais Pedagógicos --}}
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 p-4 text-center">
                            <h5 class="fw-bold">Materiais</h5>
                            <p class="small text-muted">Materiais pedagógicos acessíveis.</p>
                            <div class="form-check form-switch d-flex justify-content-center mt-3">
                                <input class="form-check-input" type="checkbox" name="materials" value="1" id="checkMat" style="transform: scale(1.5)">
                            </div>
                        </div>
                    </div>

                    {{-- Card Alunos --}}
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 p-4 text-center">
                            <h5 class="fw-bold">Alunos</h5>
                            <p class="small text-muted">Relatórios por perfil de estudante.</p>
                            <div class="form-check form-switch d-flex justify-content-center mt-3">
                                <input class="form-check-input" type="checkbox" name="students" value="1" id="checkStudents" style="transform: scale(1.5)">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-5 d-flex justify-content-center">
                    <x-buttons.submit-button class="btn-lg px-5 shadow-sm" variant="primary">
                        Prosseguir para Filtros <i class="fas fa-arrow-right ms-2"></i>
                    </x-buttons.submit-button>
                </div>
            </form>
        </div>
    </div>
@endsection
