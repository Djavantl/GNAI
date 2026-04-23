{{-- partial: disciplines-cards --}}
@php use Illuminate\Pagination\LengthAwarePaginator; @endphp

<div class="mt-4">

    @if($peiDisciplines->isEmpty())
        <div class="alert alert-secondary">Nenhuma Adaptações Razoáveis e/ou Acessibilidades Curriculares cadastrada.</div>
    @else
        <div class="row g-3">
            @foreach($peiDisciplines as $item)
                <div class="col-md-12">
                    <div class="card p-3 h-100 shadow-sm">
                        {{-- topo do card --}}
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <strong class="d-block">{{ $item->discipline->name ?? '—' }}</strong>
                                <small class="text-muted">{{ $item->teacher->person->name ?? '—' }}</small>
                            </div>

                            <div class="text-muted small flex-shrink-0">
                                {{ optional($item->created_at)->format('d/m/Y') }}
                            </div>
                        </div>

                        {{-- toggle (reusa seu section-header component) --}}
                        <x-ui.section-header
                            target="pd-{{ $item->id }}"
                            title="Mostrar adaptações"
                            description=""
                        >
                            <x-slot:actions>
                                <x-buttons.pdf-button 
                                        :href="route('specialized-educational-support.pei.discipline.pdf', [$pei, $item])" 
                                        target="_blank" 
                                    />
                            </x-slot:actions>
                        </x-ui.section-header>

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

                                <div class="col-12">
                                    <x-ui.info-card-textarea
                                        label="Registros Complementares"
                                        :value="$item->complementary_records"
                                        rows="4"
                                    />
                                </div>

                                <div class="col-12">
                                    <x-ui.info-card-textarea
                                        label="Parecer"
                                        :value="$item->opinion"
                                        rows="4"
                                    />
                                </div>

                                

                                {{-- ações --}}
                                <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                   
                                    @if(!$pei->is_finished && auth()->id() == $item->creator_id)
                                        @can('pei-discipline.update')
                                        <x-buttons.link-button Dados do PEI
                                           href="{{ route('specialized-educational-support.pei-discipline.edit', [$pei, $item]) }}"
                                            variant="warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </x-buttons.link-button>
                                        @endcan
                                        @can('pei-discipline.delete')
                                        <x-buttons.submit-button
                                            type="button"
                                            variant="danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#globalConfirmActionModal"
                                            data-confirm-title="Excluir Adaptacao"
                                            data-confirm-message="Deseja realmente excluir esta adaptacao?"
                                            data-confirm-action="{{ route('specialized-educational-support.pei-discipline.destroy', [$pei, $item]) }}"
                                            data-confirm-method="DELETE"
                                            data-confirm-submit-text="Confirmar Exclusao"
                                            data-confirm-variant="danger"
                                        >
                                                <i class="fas fa-trash-alt"></i> Excluir
                                            </x-buttons.submit-button>
                                        @endcan
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
