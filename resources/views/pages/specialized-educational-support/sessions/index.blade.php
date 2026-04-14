@extends('layouts.master')

@section('title', 'Sessões')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => null
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Sessões"
            subtitle="Veja as sessões cadastradas em tabela e acompanhe a agenda semanal abaixo."
        >
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
            <x-table.filters.form
                data-dynamic-filter
                data-target="#sessions-table"
                :fields="[
                    [
                        'name' => 'student',
                        'type' => 'select',
                        'options' => ['' => 'Aluno (Todos)'] +
                            collect($students)->mapWithKeys(fn($s) => [
                                $s->id => $s->person->name ?? 'Aluno'
                            ])->toArray()
                    ],
                    [
                        'name' => 'professional',
                        'type' => 'select',
                        'options' => ['' => 'Profissional (Todos)'] +
                            collect($professionals)->mapWithKeys(fn($p) => [
                                $p->id => $p->person->name ?? 'Profissional'
                            ])->toArray()
                    ],
                    [
                        'name' => 'type',
                        'type' => 'select',
                        'options' => [
                            '' => 'Tipo (Todos)',
                            'individual' => 'Individual',
                            'group' => 'Grupo',
                        ]
                    ],
                    [
                        'name' => 'status',
                        'type' => 'select',
                        'options' => [
                            '' => 'Status (Todos)',
                            'Agendada' => 'Agendada',
                            'Realizada' => 'Realizada',
                            'Cancelada' => 'Cancelada',
                        ]
                    ],
                ]"
            />
        </div>

        <div id="sessions-table" class="p-3 border-bottom">
            @include('pages.specialized-educational-support.sessions.partials.table')
        </div>
    </div>

    <div class="mt-5">
        <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
            <x-table.page-header
                title="Agenda semanal"
                subtitle="Visão da equipe inteira, organizada por dia da semana."
            >
            </x-table.page-header>

            <div class="px-3 pt-3">
                <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 border rounded-3 p-3 bg-light">

                    <form method="GET"
                        action="{{ route('specialized-educational-support.sessions.index') }}"
                        class="d-flex align-items-end flex-wrap gap-3">

                        @foreach(request()->except(['week', 'page']) as $key => $value)
                            @if(!is_array($value))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div>
                            <label for="week" class="form-label fw-bold mb-1">
                                Dia de referência
                            </label>

                            <input
                                type="date"
                                name="week"
                                id="week"
                                class="form-control"
                                value="{{ request('week', now()->toDateString()) }}"
                            >
                        </div>

                        <div>
                            <x-buttons.submit-button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-week"></i>
                                Mostrar semana
                            </x-buttons.submit-button>
                        </div>

                    </form>

                    <div class="d-flex gap-2 flex-wrap">
                        <x-buttons.link-button :href="$weekNavigation['previous']" variant="secondary">
                            <i class="fas fa-chevron-left"></i>
                            Semana anterior
                        </x-buttons.link-button>

                        <x-buttons.link-button :href="$weekNavigation['current']" variant="primary">
                            Semana atual
                        </x-buttons.link-button>

                        <x-buttons.link-button :href="$weekNavigation['next']" variant="secondary">
                            Próxima semana
                            <i class="fas fa-chevron-right"></i>
                        </x-buttons.link-button>
                    </div>

                </div>
            </div>

            <div class="p-3">
                @include('pages.specialized-educational-support.sessions.partials.weekly-agenda')
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/components/dynamicFilters.js')

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const storageKey = 'sessions-scroll-position-' + window.location.pathname;

                const savedScroll = sessionStorage.getItem(storageKey);

                if (savedScroll !== null) {
                    window.scrollTo({
                        top: parseInt(savedScroll, 10),
                        behavior: 'auto'
                    });
                }

                window.addEventListener('beforeunload', function () {
                    sessionStorage.setItem(storageKey, String(window.scrollY));
                });
            });
        </script>
    @endpush
@endsection