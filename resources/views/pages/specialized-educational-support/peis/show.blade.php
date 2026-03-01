@extends('layouts.app')

@section('content')

<div class="mb-5">
    <x-breadcrumb :items="[
        'Home' => route('dashboard'),
        'Alunos' => route('specialized-educational-support.students.index'),
        $pei->student->person->name => route('specialized-educational-support.students.show', $pei->student),
        'PEIs' => route('specialized-educational-support.pei.index', $pei->student),
        'PEI #' . $pei->id => null
    ]" />
</div>


{{-- ================= HEADER DA PÁGINA ================= --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="text-title mb-1">Plano Educacional Individualizado</h2>
        <div class="small text-muted">
            Versão {{ $pei->version }} • {{ $pei->semester->label }} • 
            @if($pei->is_finished)
                <span class="text-success fw-semibold">FINALIZADO</span>
            @else
                <span class="text-warning fw-semibold">EM PREENCHIMENTO</span>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        

        @if($pei->is_finished)
            <form action="{{ route('specialized-educational-support.pei.version.newVersion', $pei) }}" method="POST"
                onsubmit="return confirm('Criar nova versão baseada neste PEI?')">
                @csrf
                <x-buttons.submit-button class="btn-action new">
                    <i class="fas fa-plus"></i> Nova Versão
                </x-buttons.submit-button>
            </form>

            <x-buttons.pdf-button :href="route('specialized-educational-support.pei.pdf', $pei)" />
        @endif

        @if(!$pei->is_finished)
            <form method="POST"
                action="{{ route('specialized-educational-support.pei.finish', $pei) }}"
                onsubmit="return confirm('Após finalizar, o plano não poderá ser editado. Confirmar?')">
                @csrf
                @method('PATCH')
                <x-buttons.submit-button variant="success">
                    <i class="fas fa-check"></i> Finalizar
                </x-buttons.submit-button>
            </form>
        @endif

        <form action="{{ route('specialized-educational-support.pei.destroy', $pei) }}" method="POST"
            onsubmit="return confirm('Excluir permanentemente este PEI?')">
            @csrf 
            @method('DELETE')
            <x-buttons.submit-button variant="danger">
                <i class="fas fa-trash-alt"></i> Excluir
            </x-buttons.submit-button>
        </form>

        <x-buttons.link-button 
            :href="route('specialized-educational-support.pei.index', $pei->student)" 
            variant="secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </x-buttons.link-button>

    </div>
</div>


<div class="custom-table-card bg-white">
    <div class="row g-0">

        {{-- ================= DADOS DO PEI (CABEÇALHO COMPLETO) ================= --}}
        <x-forms.section title="Dados do PEI" />

        <div class="row g-2 px-4 pb-4">

            {{-- ===== ALUNO ===== --}}
            <div class="col-md-12">
                <div class="card p-3 border-light bg-soft-info">
                    <div class="d-flex align-items-center gap-3">

                        <img src="{{ $student->person->photo_url }}"
                            class="rounded-circle shadow-sm"
                            style="width:64px;height:64px;object-fit:cover;">

                        <div>
                            <strong class="d-block fs-5">
                                {{ $student->person->name }}
                            </strong>

                            <span class="small text-muted d-block">
                                Matrícula: {{ $student->registration ?? '—' }}
                            </span>

                            <span class="small text-muted">
                                Status:
                                @if($student->status === 'active')
                                    <span class="text-success fw-semibold">ATIVO</span>
                                @else
                                    <span class="text-danger fw-semibold">{{ strtoupper($student->status) }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            {{-- ===== DEFICIÊNCIAS ===== --}}
            <div class="col-md-12">
                <div class="row g-2">

                    @forelse($student->deficiencies as $def)
                        <x-ui.info-card
                            column="col-md-6"
                            :value="$def->name"
                        >
                            <span class="small text-muted">
                                Grau: {{ $def->pivot->severity ?? '—' }}
                            </span>
                        </x-ui.info-card>
                    @empty
                        <x-ui.info-card
                            column="col-md-6"
                            class="text-muted"
                        >
                            Nenhuma deficiência registrada.
                        </x-ui.info-card>
                    @endforelse

                </div>
            </div>


            {{-- ===== DADOS DO PEI ===== --}}
            <div class="col-md-12 pt-3">
                <div class="row g-2">

                    <x-ui.info-card
                        label="Responsável"
                        :value="$pei->creator_name"
                    />

                    <x-ui.info-card
                        label="Curso"
                        :value="$pei->course->name"
                    />

                    <x-ui.info-card
                        label="Semestre"
                        :value="$pei->semester->label"
                    />

                    <x-ui.info-card label="Versão">
                        <strong>v{{ $pei->version }}</strong>
                    </x-ui.info-card>

                    <x-ui.info-card
                        label="Criado em"
                        :value="$pei->created_at->format('d/m/Y')"
                    />

                    <x-ui.info-card label="Status">
                        @if($pei->is_finished)
                            <strong class="text-success">Finalizado</strong>
                        @else
                            <strong class="text-warning">Em preenchimento</strong>
                        @endif
                    </x-ui.info-card>

                </div>
            </div>

        </div>


        {{-- ================= CONTEXTO COMPLETO ================= --}}
        <x-forms.section title="Contexto do Estudante" />

        <div class="px-4 pb-4">
            @include('pages.specialized-educational-support.peis.partials.student-context', [
                'context' => $studentContext
            ])
        </div>

        <x-forms.section title="Adaptações Específicas" />

        <div class="px-4 pt-3 pb-2 d-flex justify-content-end align-items-center">
            @if(!$pei->is_finished)
                <x-buttons.link-button 
                    href="{{ route('specialized-educational-support.pei-discipline.create', $pei) }}" 
                    variant="primary">
                    <i class="fas fa-plus"></i> Adicionar Adaptação
                </x-buttons.link-button>
            @endif
        </div>

        {{-- adaptações por disciplina (cards paginados) --}}
        <div class="px-4 pb-4">
            @include('pages.specialized-educational-support.peis.partials.disciplines-cards', [
                'peiDisciplines' => $peiDisciplines,
                'pei' => $pei
            ])
        </div>
    </div>
</div>

@endsection