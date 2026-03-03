@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $pei->student->person->name => route('specialized-educational-support.students.show', $pei->student),
            'PEIs' => route('specialized-educational-support.pei.index', $pei->student),
            'Plano #' . $pei->id => route('specialized-educational-support.pei.show', $pei),
            'Avaliação do PEI' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-title">Avaliação do Plano Educacional Individualizado</h2>
            <p class="text-muted">Registro pedagógico institucional.</p>
        </div>

        <div class="d-flex gap-2">
            <x-buttons.link-button
                :href="route('specialized-educational-support.pei-evaluation.edit', $pei_evaluation->id)"
                variant="warning">
                <i class="fas fa-edit"></i> Editar
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.pei-evaluation.index', $pei)"
                variant="secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </x-buttons.link-button>
        </div>
    </div>

    <div class="custom-table-card bg-white">
        <div class="row g-0">

            {{-- ================= IDENTIFICAÇÃO ================= --}}
            <x-forms.section title="Identificação da Avaliação" />

            <x-show.info-item label="Aluno"
                :value="$pei->student->person->name"
                column="col-md-4"
                isBox="true" />

            <x-show.info-item label="Disciplina"
                :value="$pei->discipline->name"
                column="col-md-4"
                isBox="true" />

            <x-show.info-item label="Semestre"
                :value="$pei_evaluation->semester->label"
                column="col-md-4"
                isBox="true" />

            <x-show.info-item label="Tipo de Avaliação"
                :value="$pei_evaluation->evaluation_type->label()"
                column="col-md-4"
                isBox="true" />

            <x-show.info-item label="Data da Avaliação"
                :value="$pei_evaluation->evaluation_date->format('d/m/Y')"
                column="col-md-4"
                isBox="true" />

            <x-show.info-item label="Profissional Responsável"
                :value="$pei_evaluation->professional->person->name"
                column="col-md-4"
                isBox="true" />

            {{-- ================= INSTRUMENTOS ================= --}}
            <x-forms.section title="Instrumentos de Avaliação" />

            <x-show.info-textarea
                label="Instrumentos Utilizados"
                :value="$pei_evaluation->evaluation_instruments"
                column="col-md-12"
                isBox="true" />

            {{-- ================= PARECER PEDAGÓGICO ================= --}}
            <x-forms.section title="Parecer Descritivo" />

            <x-show.info-textarea
                label="Análise do Desempenho do Estudante"
                :value="$pei_evaluation->parecer"
                column="col-md-12"
                isBox="true" />

            {{-- ================= ESTRATÉGIAS EFICAZES ================= --}}
            <x-forms.section title="Estratégias com Êxito" />

            <x-show.info-textarea
                label="Metodologias e Adaptações que Funcionaram"
                :value="$pei_evaluation->successful_proposals"
                column="col-md-12"
                isBox="true" />

            {{-- ================= METAS FUTURAS ================= --}}
            <x-forms.section title="Metas para Próxima Etapa" />

            <x-show.info-textarea
                label="Encaminhamentos Pedagógicos"
                :value="$pei_evaluation->next_stage_goals ?? 'Não informado'"
                column="col-md-12"
                isBox="true" />

            <footer class="col-12 border-top p-4 d-flex justify-content-between align-items-center bg-light-subtle">
                <div class="text-muted small d-flex align-items-center">
                    <i class="fas fa-id-card me-1" aria-hidden="true"></i> ID no Sistema: #{{ $pei->id }}
                    {{-- Botão de PDF --}}
                    @if($pei->is_finished)
                        <x-buttons.pdf-button class="ms-3" :href="route('specialized-educational-support.pei-evaluation.pdf', $pei_evaluation)" />
                    @endif
                </div>
                <div class="d-flex gap-2" role="group" aria-label="Ações de gestão">
                    <form action="{{ route('specialized-educational-support.pei-evaluation.destroy', $pei_evaluation) }}" method="POST" onsubmit="return confirm('Deseja excluir permanentemente?')">
                        @csrf @method('DELETE')
                        <x-buttons.submit-button variant="danger">
                            <i class="fas fa-trash-alt"></i> Excluir
                        </x-buttons.submit-button>
                    </form>
                    <x-buttons.link-button :href="route('specialized-educational-support.pei-evaluation.index', $pei)" variant="secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </x-buttons.link-button>
                </div>
            </footer>

        </div>
    </div>

@endsection
