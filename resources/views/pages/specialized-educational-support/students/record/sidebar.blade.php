{{-- SIDEBAR LATERAL --}}
            <div class="col-md-3 border-end p-4 bg-soft-info">
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <img src="{{ $student->person->photo_url }}" class="rounded-circle mb-3 shadow-sm" style="width:140px;height:140px;object-fit:cover;border:4px solid #fff">
                        <span class="badge bg-{{ $student->status?->color() ?? 'secondary' }} position-absolute bottom-0 end-0 mb-3 me-2 border border-2 border-white p-2">
                            {{ strtoupper($student->status?->label() ?? '—') }}
                        </span>
                    </div>

                    <h4 class="mb-0 fw-bold text-dark">{{ $student->person->name }}</h4>
                    <p class="text-muted small">Matrícula: {{ $student->registration }}</p>
                </div>

                <div class="d-grid gap-2 mb-4">
                    <x-buttons.link-button :href="route('specialized-educational-support.students.edit', $student)" variant="warning" class="btn-sm">
                        <i class="fas fa-edit me-1"></i> Editar Cadastro
                    </x-buttons.link-button>
                    
                     <x-buttons.link-button
                        href="{{ route('specialized-educational-support.students.logs.index', $student) }}"
                        variant="info"
                        class="btn-sm"
                    >
                        <i class="fas fa-history me-1"></i> Histórico de Alterações
                    </x-buttons.link-button>
                    <x-buttons.submit-button
                        type="button"
                        variant="danger"
                        class="btn-sm w-100"
                        data-bs-toggle="modal"
                        data-bs-target="#globalConfirmActionModal"
                        data-confirm-title="Excluir Aluno"
                        data-confirm-message="Excluir este aluno do sistema?"
                        data-confirm-action="{{ route('specialized-educational-support.students.destroy', $student) }}"
                        data-confirm-method="DELETE"
                        data-confirm-submit-text="Confirmar Exclusao"
                        data-confirm-variant="danger"
                    >
                            <i class="fas fa-trash-alt me-1"></i> Excluir
                        </x-buttons.submit-button>

                </div>

                <hr class="my-4 text-muted">

                {{-- MENU INTERNO (ÍCONES EM VEZ DE EMOJIS) --}}
                <div class="list-group list-group-flush shadow-sm rounded" id="student-menu">
                    <a href="#dados-gerais" class="list-group-item list-group-item-action active">
                        <i class="fas fa-id-card me-2"></i> Dados Gerais
                    </a>
                    <a href="#informacoes-escolares" class="list-group-item list-group-item-action">
                        <i class="fas fa-graduation-cap me-2"></i> Vida Escolar
                    </a>
                    <a href="#deficiencias" class="list-group-item list-group-item-action">
                        <i class="fas fa-wheelchair me-2"></i> Perfis de Atendimento
                    </a>
                    <a href="#responsaveis" class="list-group-item list-group-item-action">
                        <i class="fas fa-users me-2"></i> Responsáveis
                    </a>
                    <a href="#contextos" class="list-group-item list-group-item-action">
                        <i class="fas fa-clipboard-list me-2"></i> Contextos Avaliativos
                    </a>
                    <a href="#peis" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-alt me-2"></i> PEIs Realizados
                    </a>
                </div>

                <div class="mt-4 p-3 bg-white rounded border border-dashed text-center">
                    <span class="text-muted small d-block">Sistema NAPNE</span>
                    <strong class="small text-secondary">ID #{{ $student->id }}</strong>
                </div>
            </div>
