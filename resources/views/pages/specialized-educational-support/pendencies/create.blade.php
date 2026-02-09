@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Criar Nova Pendência</h2>
            <p class="text-muted">Registre uma nova pendência e direcione-a a um profissional responsável.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pendencies.store') }}" method="POST">
            @csrf

            <x-forms.section title="Dados da Pendência" />

            <div class="col-md-12">
                <x-forms.input
                    name="title"
                    label="Título *"
                    required
                    :value="old('title')"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição"
                    rows="4"
                >{{ old('description') }}</x-forms.textarea>
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="assigned_to"
                    label="Profissional Responsável *"
                    :options="$professionals->pluck('name','id')->toArray()"
                    :value="old('assigned_to')"
                    required
                />
            </div>

            <div class="col-md-3">
                <x-forms.select
                    name="priority"
                    label="Prioridade *"
                    :options="[
                        'urgent' => 'Urgente',
                        'high'   => 'Alta',
                        'medium' => 'Média',
                        'low'    => 'Baixa'
                    ]"
                    :value="old('priority', 'medium')"
                    required
                />
            </div>

            <div class="col-md-3">
                <x-forms.input
                    name="due_date"
                    label="Data de Vencimento"
                    type="date"
                    :value="old('due_date')"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pendencies.index') }}" variant="secondary">
                    Voltar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save me-2"></i> Salvar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection
