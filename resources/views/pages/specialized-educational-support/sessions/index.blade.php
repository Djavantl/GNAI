@extends('layouts.master')

@section('title', 'Sessões')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => null
        ]" />
    </div>

    {{-- AGENDA SEMANAL NO TOPO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden mb-5">
        <x-table.page-header
            title="Agenda semanal"
            subtitle="Visão da equipe inteira, organizada por dia da semana."
        >
            {{-- BOTÕES ORIGINAIS MOVIDOS PARA O HEADER DA AGENDA --}}
            <x-buttons.link-button
                :href="route('specialized-educational-support.sessions.create')"
                variant="new"
                title="Nova sessão"
            >
                <i class="fas fa-plus"></i> 
            </x-buttons.link-button>

            @if(auth()->user()->professional)
                <x-buttons.link-button
                    :href="route('specialized-educational-support.sessions.my-sessions')"
                    variant="secondary"
                    title="Minhas sessões"
                >
                    <i class="fas fa-user-clock"></i> Minhas sessões
                </x-buttons.link-button>
            @endif
        </x-table.page-header>

        <div class="px-3 pt-3">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 border rounded-3 p-3 bg-light">
                
                <form method="GET" action="{{ route('specialized-educational-support.sessions.index') }}" class="d-flex align-items-end flex-wrap gap-3">
                    {{-- Mantém os filtros da tabela ao navegar na agenda --}}
                    @foreach(request()->except(['week', 'student_agenda', 'professional_agenda']) as $key => $value)
                        @if(!is_array($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <div>
                        <label for="week" class="form-label fw-bold mb-1 small">Dia de referência</label>
                        <input type="date" name="week" id="week" class="form-control" value="{{ request('week', now()->toDateString()) }}">
                    </div>

                    {{-- NOVOS FILTROS DA AGENDA --}}
                    <div>
                        <label for="student_agenda" class="form-label fw-bold mb-1 small">Filtrar Aluno</label>
                        <select name="student_agenda" id="student_agenda" class="form-select">
                            <option value="">Todos os Alunos</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" {{ request('student_agenda') == $s->id ? 'selected' : '' }}>
                                    {{ $s->person->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="professional_agenda" class="form-label fw-bold mb-1 small">Filtrar Professor</label>
                        <select name="professional_agenda" id="professional_agenda" class="form-select">
                            <option value="">Todos os Professores</option>
                            @foreach($professionals as $p)
                                <option value="{{ $p->id }}" {{ request('professional_agenda') == $p->id ? 'selected' : '' }}>
                                    {{ $p->person->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <x-buttons.submit-button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </x-buttons.submit-button>

                        <x-buttons.link-button
                            :href="route('specialized-educational-support.sessions.index')"
                            variant="info"
                        >
                        <i class="fas fa-eraser" aria-hidden="true"></i> Limpar
                        </x-buttons.link-button>

                    </div>
                </form>

                <div class="d-flex gap-2 flex-wrap">
                    <x-buttons.link-button :href="$weekNavigation['previous']" variant="secondary">
                        <i class="fas fa-chevron-left"></i> Semana anterior
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="$weekNavigation['current']" variant="primary">
                        Semana atual
                    </x-buttons.link-button>

                    <x-buttons.link-button :href="$weekNavigation['next']" variant="secondary">
                        Próxima semana <i class="fas fa-chevron-right"></i>
                    </x-buttons.link-button>
                </div>
            </div>
        </div>

        <div class="p-3">
            @include('pages.specialized-educational-support.sessions.partials.weekly-agenda')
        </div>
    </div>

    {{-- TABELA DE SESSÕES ABAIXO --}}
    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Sessões"
            subtitle="Veja as sessões cadastradas em tabela e acompanhe a agenda semanal acima."
        />

        <div class="px-3 pt-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="flex-grow-1">
                    <x-table.filters.form
                        data-dynamic-filter
                        data-target="#sessions-table"
                        :fields="[
                            [
                                'name' => 'student',
                                'type' => 'select',
                                'options' => ['' => 'Aluno (Todos)'] + collect($students)->mapWithKeys(fn($s) => [$s->id => $s->person->name ?? 'Aluno'])->toArray()
                            ],
                            [
                                'name' => 'professional',
                                'type' => 'select',
                                'options' => ['' => 'Profissional (Todos)'] + collect($professionals)->mapWithKeys(fn($p) => [$p->id => $p->person->name ?? 'Profissional'])->toArray()
                            ],
                            [
                                'name' => 'type',
                                'type' => 'select',
                                'options' => ['' => 'Tipo (Todos)', 'individual' => 'Individual', 'group' => 'Grupo']
                            ],
                            [
                                'name' => 'status',
                                'type' => 'select',
                                'options' => ['' => 'Status (Todos)', 'Agendada' => 'Agendada', 'Realizada' => 'Realizada', 'Cancelada' => 'Cancelada']
                            ],
                        ]"
                    />
                </div>
            </div>
        </div>

        <div id="sessions-table" class="p-3 border-bottom">
            @include('pages.specialized-educational-support.sessions.partials.table')
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')
        <script>
            // Mantive seu script de persistência de scroll original
            document.addEventListener('DOMContentLoaded', function () {
                const storageKey = 'sessions-scroll-position-' + window.location.pathname;
                const savedScroll = sessionStorage.getItem(storageKey);
                if (savedScroll !== null) {
                    window.scrollTo({ top: parseInt(savedScroll, 10), behavior: 'auto' });
                }
                window.addEventListener('beforeunload', function () {
                    sessionStorage.setItem(storageKey, String(window.scrollY));
                });
            });
        </script>
    @endpush
@endsection