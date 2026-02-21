{{-- PEIs --}}
<section id="peis" class="mb-5 bg-soft-info rounded shadow-sm">

    <x-forms.section title="PEIs (Plano de Ensino Individualizado)" class="m-0" />

    <div class="pb-3 ps-3 pe-3">

        <div class="table-responsive">
            <x-table.table :headers="['Curso', 'Disciplina', 'Professor', 'Status', '']">

                @forelse($student->peis as $pei)
                    <tr>

                        {{-- CURSO --}}
                        <x-table.td>
                            <span class="fw-bold text-purple-dark">
                                {{ $pei->course->name ?? 'Geral' }}
                            </span>
                        </x-table.td>

                        {{-- CURSO --}}
                        <x-table.td>
                            
                            {{ $pei->discipline->name ?? 'Geral' }}
                            
                        </x-table.td>

                        {{-- PROFESSOR --}}
                        <x-table.td>
                            {{ $pei->teacher_name ?? 'Não informado' }}
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
                        <x-table.td>
                            <x-table.actions>

                                <x-buttons.link-button
                                    :href="route('specialized-educational-support.pei.show', $pei->id)"
                                    variant="info"
                                    class="btn-sm"
                                >
                                    <i class="fas fa-eye"></i>
                                </x-buttons.link-button>

                            </x-table.actions>
                        </x-table.td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Nenhum PEI encontrado para este aluno.
                        </td>
                    </tr>
                @endforelse

            </x-table.table>
        </div>

        {{-- BOTÃO GERENCIAR --}}
        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
            <x-buttons.link-button
                :href="route('specialized-educational-support.pei.index', $student)"
                variant="warning"
                class="btn-sm">
                <i class="fas fa-folder-open"></i> Gerenciar
            </x-buttons.link-button>
        </div>

    </div>
</section>
