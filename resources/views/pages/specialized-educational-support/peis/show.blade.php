@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $pei->student->person->name => route('specialized-educational-support.students.show', $pei->student),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student),
            'Plano #' . $pei->id => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Plano Educacional Individualizado</h2>
            <p class="text-muted">Visualize o Plano educacional individualizado e todas suas informações.</p>
        </div>

        <div class="d-flex gap-2">
         
            @if($pei->is_finished)
                <form 
                    action="{{ route('specialized-educational-support.pei.version.newVersion', $pei) }}" 
                    method="POST"
                    onsubmit="return confirm('Será criada uma nova versão baseada neste PEI. Deseja continuar?')"
                >
                    @csrf

                    <x-buttons.submit-button type="submit" class="btn-action new">
                        <i class="fas fa-plus"></i> Nova Versão
                    </x-buttons.submit-button>
                </form>
            @endif

            <x-buttons.link-button 
                :href="route('specialized-educational-support.pei-evaluation.index', $pei)" 
                variant="info">
                <i class="fas fa-clipboard-check"></i> Avaliações
            </x-buttons.link-button>

            @if(!$pei->is_finished)
                <x-buttons.link-button 
                    :href="route('specialized-educational-support.pei.edit', $pei->id)" 
                    variant="warning">
                   <i class="fas fa-edit"></i> Editar 
                </x-buttons.link-button>
            @endif

            <x-buttons.link-button 
                :href="route('specialized-educational-support.pei.index', $pei->student->id)" 
                variant="secondary">
                <i class="fas fa-arrow-left"></i>Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white">
        <div class="row g-0">

            {{-- ================= IDENTIFICAÇÃO ================= --}}
            <x-forms.section title="Identificação do Plano" />

            <x-show.info-item label="Aluno" 
                :value="$pei->student->person->name" 
                column="col-md-4" 
                isBox="true" />

            <x-show.info-item label="Profissional Responsável" 
                :value="$pei->professional->person->name" 
                column="col-md-4" 
                isBox="true" />
            
            <x-show.info-item label="Semestre" 
                :value="$pei->semester->label" 
                column="col-md-4" 
                isBox="true" />

            <x-show.info-item label="Curso" 
                :value="$pei->course->name" 
                column="col-md-4" 
                isBox="true" />

            <x-show.info-item label="Disciplina" 
                :value="$pei->discipline->name" 
                column="col-md-4" 
                isBox="true" />

            <x-show.info-item label="Versão" 
                :value="$pei->version" 
                column="col-md-4" 
                isBox="true" />

            <x-show.info-item label="Docente" 
                :value="$pei->teacher_name" 
                column="col-md-6" 
                isBox="true" />

            <x-show.info-item label="Status" column="col-md-6" isBox="true">
                @if($pei->is_finished)
                    <span class="text-success">FINALIZADO</span>
                @else
                    <span class="text-warning">EM PREENCHIMENTO</span>
                @endif
            </x-show.info-item>

            {{-- ================= CONTEXTO DO ALUNO ================= --}}
            <x-forms.section title="Contexto do Aluno" />

            <x-show.info-item 
                label="Contexto Pedagógico" 
                column="col-md-12" 
                isBox="true">
                <a href="{{ route('specialized-educational-support.student-context.pdf', $pei->student_context_id) }}" 
                    class="text-purple-dark fw-bold small" 
                    target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i> Ver Contexto
                </a>
            </x-show.info-textarea>


            {{-- ================= OBJETIVOS ================= --}}
            <x-forms.section title="Objetivos Específicos" />

            <div class="col-12 px-4 pb-4">

                <div class="d-flex justify-content-end mb-3">
                    @if(!$pei->is_finished)
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pei.objective.create', $pei->id)"
                            variant="new">
                            <i class="fas fa-plus"></i>Adicionar Objetivo
                        </x-buttons.link-button>
                    @endif
                </div>

                <x-table.table :headers="['Descrição', 'Status', '']">
                    @forelse($pei->specificObjectives as $obj)
                        <tr>
                            <x-table.td>{{ $obj->description }}</x-table.td>
                            <x-table.td>{{ $obj->status->label() }}</x-table.td>
    
                            <x-table.td>
                                @if(!$pei->is_finished)
                                    <x-table.actions class="d-flex justify-content-end">
                                        <x-buttons.link-button href="{{ route('specialized-educational-support.pei.objective.show', $obj) }}" variant="info">
                                           <i class="fas fa-eye"></i> Ver
                                        </x-buttons.link-button>
                                        
                                    </x-table.actions>
                                @endif
                            </x-table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Nenhum objetivo cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </x-table.table>

            </div>


            {{-- ================= CONTEÚDO PROGRAMÁTICO ================= --}}
            <x-forms.section title="Conteúdo Programático" />

            <div class="col-12 px-4 pb-4">

                <div class="d-flex justify-content-end mb-3">
                    @if(!$pei->is_finished)
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pei.content.create', $pei->id)"
                            variant="new">
                           <i class="fas fa-plus"></i> Adicionar Conteúdo
                        </x-buttons.link-button>
                    @endif
                </div>

                <x-table.table :headers="['Título', 'Descrição', '']">
                    @forelse($pei->contentProgrammatic as $content)
                        <tr>
                            <x-table.td>{{ $content->title }}</x-table.td>
                            <x-table.td>{{ $content->description ?? '---' }}</x-table.td>
                            <x-table.td>
                                @if(!$pei->is_finished)
                                    <x-table.actions class="d-flex justify-content-end">
                                        <x-buttons.link-button href="{{ route('specialized-educational-support.pei.content.show', $content) }}" variant="info">
                                            <i class="fas fa-eye"></i>Ver
                                        </x-buttons.link-button>

                                        
                                    </x-table.actions>
                                @endif
                            </x-table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                Nenhum conteúdo cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </x-table.table>

            </div>


            {{-- ================= METODOLOGIAS ================= --}}
            <x-forms.section title="Metodologias" />

            <div class="col-12 px-4 pb-4">

                <div class="d-flex justify-content-end mb-3">
                    @if(!$pei->is_finished)
                        <x-buttons.link-button
                            :href="route('specialized-educational-support.pei.methodology.create', $pei->id)"
                            variant="new">
                           <i class="fas fa-plus"></i> Adicionar Metodologia
                        </x-buttons.link-button>
                    @endif
                </div>

                <x-table.table :headers="['Descrição', 'Recursos Utilizados', '']">
                    @forelse($pei->methodologies as $method)
                        <tr>
                            <x-table.td>{{ $method->description }}</x-table.td>
                            <x-table.td>{{ $method->resources_used ?? '---' }}</x-table.td>
                            <x-table.td>
                                @if(!$pei->is_finished)
                                    <x-table.actions class="d-flex justify-content-end">
                                        <x-buttons.link-button href="{{ route('specialized-educational-support.pei.methodology.show', $method) }}" variant="info">
                                            <i class="fas fa-eye"></i>Ver
                                        </x-buttons.link-button>

                                    
                                    </x-table.actions>
                                @endif
                            </x-table.td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                Nenhuma metodologia cadastrada.
                            </td>
                        </tr>
                    @endforelse
                </x-table.table>

            </div>


            {{-- ================= RODAPÉ ================= --}}
           <div class="col-12 border-top p-4 d-flex justify-content-end gap-3">

                {{-- Botão de PDF --}}
                <x-buttons.pdf-button :href="route('specialized-educational-support.pei.pdf', $pei->id)" />

                {{-- Botão de Finalizar --}}
                @if(!$pei->is_finished)
                    <form method="POST"
                        action="{{ route('specialized-educational-support.pei.finish', $pei->id) }}"
                        onsubmit="return confirm('Após finalizar, o plano não poderá mais ser editado. Confirmar?')">
                        @csrf
                        @method('PATCH')

                        <x-buttons.submit-button variant="success">
                            <i class="fas fa-check"></i> Finalizar Plano
                        </x-buttons.submit-button>
                    </form>
                @endif

            </div>

        </div>
    </div>

@endsection
