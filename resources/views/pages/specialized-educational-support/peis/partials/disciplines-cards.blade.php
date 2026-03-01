{{-- partial: disciplines-cards --}}
@php use Illuminate\Pagination\LengthAwarePaginator; @endphp

<div class="mt-4">

    @if($peiDisciplines->isEmpty())
        <div class="alert alert-secondary">Nenhuma adaptação por disciplina cadastrada.</div>
    @else
        <div class="row g-3">
            @foreach($peiDisciplines as $item)
                <div class="col-md-12">
                    <div class="card p-3 h-100 shadow-sm">
                        {{-- topo do card --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong class="d-block">{{ $item->discipline->name ?? '—' }}</strong>
                                <small class="text-muted">{{ $item->teacher->person->name ?? '—' }}</small>
                            </div>

                            <div class="text-muted small">
                                {{ optional($item->created_at)->format('d/m/Y') }}
                            </div>
                        </div>

                        {{-- toggle (reusa seu section-header component) --}}
                        <x-ui.section-header
                            target="pd-{{ $item->id }}"
                            title="Mostrar adaptações"
                            description=""
                        />

                        {{-- conteúdo colapsável --}}
                        <div id="pd-{{ $item->id }}" class="ctx-collapsed">
                            <div class="row g-2 mt-2">
                                <div class="col-12">
                                    <x-ui.info-card-textarea
                                        label="Objetivos Específicos"
                                        :value="$item->specific_objectives"
                                        rows="4"
                                    />
                                </div>

                                <div class="col-12">
                                    <x-ui.info-card-textarea
                                        label="Conteúdo Programático"
                                        :value="$item->content_programmatic"
                                        rows="4"
                                    />
                                </div>

                                <div class="col-12">
                                    <x-ui.info-card-textarea
                                        label="Metodologias"
                                        :value="$item->methodologies"
                                        rows="4"
                                    />
                                </div>

                                <div class="col-12">
                                    <x-ui.info-card-textarea
                                        label="Avaliações"
                                        :value="$item->evaluations"
                                        rows="4"
                                    />
                                </div>

                                {{-- ações --}}
                                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                    <x-buttons.link-button 
                                        href="{{ route('specialized-educational-support.pei-discipline.show', [$pei, $item]) }}"
                                        variant="info">
                                        <i class="fas fa-eye"></i> Ver
                                    </x-buttons.link-button>
                                    @if(!$pei->is_finished)
                                        <x-buttons.link-button Dados do PEI
                                           href="{{ route('specialized-educational-support.pei-discipline.edit', [$pei, $item]) }}"
                                            variant="warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </x-buttons.link-button>
                                        <form action="{{ route('specialized-educational-support.pei-discipline.destroy', [$pei, $item]) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Deseja realmente excluir esta Adaptação?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-buttons.submit-button variant="danger">
                                                <i class="fas fa-trash-alt"></i> Excluir
                                            </x-buttons.submit-button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- paginador reutilizável --}}
        <div class="mt-4">
            <x-ui.pagination :records="$peiDisciplines" />
        </div>
    @endif

</div>