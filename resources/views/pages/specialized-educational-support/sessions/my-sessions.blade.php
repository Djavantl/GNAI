@extends('layouts.master')

@section('title', 'Minhas Sessões')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            'Minhas Sessões' => null
        ]" />
    </div>

    <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
        <x-table.page-header
            title="Minhas Sessões"
            subtitle="Visualize as sessões em que você está vinculado como profissional responsável."
        >
            <x-buttons.link-button
                :href="route('specialized-educational-support.sessions.create')"
                variant="new"
                title="Nova sessão"
            >
                <i class="fas fa-plus"></i>
            </x-buttons.link-button>

            <x-buttons.link-button
                :href="route('specialized-educational-support.sessions.index')"
                variant="secondary"
                title="Voltar para sessões"
            >
                <i class="fas fa-arrow-left"></i>
            </x-buttons.link-button>
        </x-table.page-header>

        <div class="px-3 pt-3">
            <x-table.filters.form
                data-dynamic-filter
                data-target="#my-sessions-table"
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

        <div id="my-sessions-table" class="p-3 border-bottom">
            @include('pages.specialized-educational-support.sessions.partials.my-table')
        </div>
    </div>

    <div class="mt-5">
        <div class="custom-table-card shadow-sm border rounded-3 overflow-hidden">
            <x-table.page-header
                title="Minha agenda semanal"
                subtitle="Somente as sessões do profissional logado."
            >
            </x-table.page-header>

            <div class="px-3 pt-3">
                <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 border rounded-3 p-3 bg-light">

                    <form method="GET"
                        action="{{ route('specialized-educational-support.sessions.my-sessions') }}"
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