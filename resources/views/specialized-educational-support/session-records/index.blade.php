<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Sessão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .section-title { 
            border-left: 5px solid #6f42c1; 
            padding-left: 10px; 
            margin-bottom: 20px; 
            color: #6f42c1;
            margin-top: 30px;
        }
        .card-header {
            background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 12px;
        }
        .empty-state { 
            padding: 60px 20px; 
            text-align: center; 
        }
        .empty-state-icon { 
            font-size: 4rem; 
            color: #dee2e6; 
            margin-bottom: 20px; 
        }
        .table-hover tbody tr:hover {
            background-color: rgba(111, 66, 193, 0.05);
        }
        .truncate-text {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Cabeçalho -->
        <div class="card shadow border-0 mb-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center p-3">
                <div>
                    <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Registros de Sessão</h4>
                    <p class="mb-0 mt-2 opacity-75">Registros de atendimentos educacionais especializados</p>
                </div>
                @if(request()->has('session_id'))
                    <a href="{{ route('specialized-educational-support.sessions.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar para Sessões
                    </a>
                @endif
            </div>
        </div>

        <!-- Mensagens -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filtros -->
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Aluno</label>
                        <select class="form-select form-select-sm" id="filter-student">
                            <option value="">Todos os alunos</option>
                            <!-- Opções de alunos seriam carregadas via AJAX ou Laravel -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Data Início</label>
                        <input type="date" class="form-control form-control-sm" id="filter-start-date">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Data Fim</label>
                        <input type="date" class="form-control form-control-sm" id="filter-end-date">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if($sessionRecords->isEmpty())
            <!-- Estado vazio -->
            <div class="card shadow border-0">
                <div class="card-body empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-journal-text" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-secondary">Nenhum registro encontrado</h3>
                    <p class="text-muted mb-4">Não há registros de sessão cadastrados no sistema.</p>
                    
                    @if(request()->has('session_id'))
                        <a href="{{ route('specialized-educational-support.session-records.create', request('session_id')) }}" class="btn btn-primary btn-lg px-5 shadow-sm">
                            <i class="bi bi-plus-circle me-2"></i> Criar Primeiro Registro
                        </a>
                    @else
                        <p class="text-muted">Acesse uma sessão para criar um registro.</p>
                    @endif
                </div>
            </div>
        @else
            <!-- Lista de Registros -->
            <div class="card shadow border-0">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark"><i class="bi bi-list-check me-2"></i>Registros ({{ $sessionRecords->count() }})</h5>
                        @if(request()->has('session_id'))
                            <a href="{{ route('specialized-educational-support.session-records.create', request('session_id')) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Novo Registro
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Data</th>
                                    <th>Aluno</th>
                                    <th>Duração</th>
                                    <th>Participação</th>
                                    <th>Avaliação</th>
                                    <th class="text-end pe-4">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sessionRecords as $record)
                                <tr>
                                    <td class="ps-4">
                                        <strong>{{ $record->record_date ? \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') : 'N/A' }}</strong>
                                        <div class="small text-muted">{{ $record->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        @if($record->session && $record->session->student)
                                            <strong>{{ $record->session->student->person->name ?? 'Aluno não encontrado' }}</strong>
                                            <div class="small text-muted">
                                                Sessão #{{ $record->attendance_sessions_id }}
                                            </div>
                                        @else
                                            <span class="text-muted">Sessão não encontrada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info status-badge">
                                            {{ $record->duration }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="truncate-text" title="{{ $record->student_participation }}">
                                            {{ $record->student_participation }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="truncate-text" title="{{ $record->development_evaluation }}">
                                            {{ Str::limit($record->development_evaluation, 50) }}
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('specialized-educational-support.session-records.show', $record) }}" 
                                               class="btn btn-sm btn-outline-info"
                                               data-bs-toggle="tooltip" title="Visualizar">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('specialized-educational-support.session-records.edit', $record) }}" 
                                               class="btn btn-sm btn-outline-warning"
                                               data-bs-toggle="tooltip" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('specialized-educational-support.session-records.destroy', $record) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja remover este registro?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- EM VEZ DISSO, MOSTRAR APENAS O TOTAL DE REGISTROS -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Total de {{ $sessionRecords->count() }} registro(s)
                            </small>
                        </div>
                        <div>
                            <!-- Se quiser adicionar paginação futuramente, você pode usar:
                            {{-- $sessionRecords->links() --}}
                            -->
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Rodapé -->
        <div class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('specialized-educational-support.sessions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para Sessões
                    </a>
                </div>
                <div class="col-md-6 text-end">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i> Ações em Massa
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i> Exportar Relatório</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i> Imprimir Lista</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> Limpar Filtros</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ativar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
</body>
</html>