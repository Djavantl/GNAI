{{-- PEIs --}}
<section id="peis" class="mb-5  rounded shadow-sm">

    <x-forms.section title="PEIs (Plano Educacional Individualizado)" class="m-0" />

    <div class="pb-3 ps-3 pe-3">

        <div class="table-responsive">
            <x-table.table :headers="[
                ['label' => 'Semestre', 'responsive' => false],
                'Curso',
                'Professor',
                'Status',
                ['label' => '', 'responsive' => false],
            ]">

                @forelse($student->peis as $pei)
                    <tr>

                        {{-- SEMESTRE --}}
                        <x-table.td :responsive="false">
                            <span class="fw-bold text-purple-dark">
                                {{ $pei->semester->label ?? 'Não informado' }}
                            </span>
                        </x-table.td>

                        {{-- CURSO --}}
                        <x-table.td>
                            {{ $pei->course->name ?? 'Geral' }}
                        </x-table.td>

                        {{-- PROFESSOR --}}
                        <x-table.td>
                            {{ $pei->peiDisciplines->pluck('teacher.person.name')->filter()->unique()->join(', ') ?: 'Não informado' }}
                        </x-table.td>

                        {{-- STATUS --}}
                        <x-table.td>
                            @if($pei->is_finished)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i> Finalizado
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i> Em andamento
                                </span>
                            @endif
                        </x-table.td>

                        {{-- AÇÕES --}}
                        <x-table.td :responsive="false">
                            <x-table.actions>
                                @can('pei.view')
                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.pei.show', $pei->id)"
                                    variant="info"
                                    class="btn-sm"
                                >
                                    <i class="fas fa-eye"></i>
                                </x-buttons.link-button>
                                @endcan
                            </x-table.actions>
                        </x-table.td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted fw-bold py-5">
                            <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                            Nenhum PEI encontrado.
                        </td>
                    </tr>
                @endforelse

            </x-table.table>
        </div>

        {{-- BOTÃO GERENCIAR --}}
        @can('pei.view')
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mt-4 pt-3 border-top">
                <small class="text-muted">
                    São exibidos até 5 registros mais recentes. Para consultar todos, clique em “Gerenciar PEIs”.
                </small>
                <x-buttons.link-button
                    :href="route('specialized-educational-support.pei.index', $student)"
                    variant="warning"
                    class="btn-sm">
                    <i class="fas fa-folder-open"></i> Gerenciar PEIs
                </x-buttons.link-button>
            </div>
        @endcan

    </div>
</section>
