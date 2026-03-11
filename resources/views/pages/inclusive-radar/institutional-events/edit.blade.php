@extends('layouts.master')

@section('title', 'Editar - Agenda Institucional')

@section('content')
    <div class="mb-5">
        <nav aria-label="Breadcrumb">
            <x-breadcrumb :items="[
                'Home' => route('dashboard'),
                'Agenda Institucional' => route('inclusive-radar.institutional-events.index'),
                'Editar' => null
            ]" />
        </nav>
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <header>
            <h1 class="text-title h2">Editar Registro da Agenda</h1>
            <p class="text-muted mb-0">Atualize as informações do evento ou compromisso na agenda institucional.</p>
        </header>
        <div>
            <x-buttons.link-button
                href="{{ route('inclusive-radar.institutional-events.index') }}"
                variant="secondary"
                label="Cancelar edição e voltar para a lista de agenda"
            >
                <i class="fas fa-times" aria-hidden="true"></i> Cancelar
            </x-buttons.link-button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
            <p class="font-weight-bold mb-1"><i class="fas fa-exclamation-triangle mr-2" aria-hidden="true"></i> <strong>Atenção:</strong> Existem erros no preenchimento.</p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-3">
        <x-forms.form-card action="{{ route('inclusive-radar.institutional-events.update', $event) }}" method="POST">
            @csrf
            @method('PUT')

            <x-forms.section title="Informações Principais" />

            <div class="col-md-12">
                <x-forms.input
                    name="title"
                    label="Título"
                    required
                    aria-required="true"
                    placeholder="Ex: Reunião de Coordenação"
                    :value="$event->title"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição"
                    rows="3"
                    placeholder="Detalhes sobre o evento"
                    :value="$event->description"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    type="date"
                    name="start_date"
                    label="Data de Início"
                    required
                    :value="$event->start_date?->format('Y-m-d')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    type="time"
                    name="start_time"
                    label="Horário de Início"
                    required
                    :value="$event->start_time?->format('H:i')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    type="date"
                    name="end_date"
                    label="Data de Término"
                    required
                    :value="$event->end_date?->format('Y-m-d')"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    type="time"
                    name="end_time"
                    label="Horário de Término"
                    required
                    :value="$event->end_time?->format('H:i')"
                />
            </div>

            <x-forms.section title="Detalhes Adicionais" />

            <div class="col-md-6">
                <x-forms.input
                    name="location"
                    label="Local"
                    placeholder="Ex: Sala de Reuniões 1"
                    :value="$event->location"
                />
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="organizer"
                    label="Organizador"
                    placeholder="Ex: Coordenação Pedagógica"
                    :value="$event->organizer"
                />
            </div>

            <div class="col-md-12">
                <x-forms.input
                    name="audience"
                    label="Público Alvo"
                    placeholder="Ex: Professores, Equipe Administrativa"
                    :value="$event->audience"
                />
            </div>

            <x-forms.section title="Configurações" />

            <div class="col-md-6">
                <x-forms.checkbox
                    name="is_active"
                    label="Ativo"
                    description="Será exibido na agenda institucional"
                    :checked="$event->is_active"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-top pt-4 px-4 pb-4 mt-4">
                <x-buttons.link-button
                    href="{{ route('inclusive-radar.institutional-events.index') }}"
                    variant="secondary"
                    label="Cancelar edição e voltar"
                >
                    <i class="fas fa-times" aria-hidden="true"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit" aria-label="Salvar alterações">
                    <i class="fas fa-save me-1" aria-hidden="true"></i> Atualizar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection
