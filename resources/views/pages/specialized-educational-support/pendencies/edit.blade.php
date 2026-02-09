@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Editar Pendência</h2>
            <p class="text-muted">Atualize os dados da pendência.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pendencies.update', $pendency) }}" method="POST">
            @csrf
            @method('PUT')

            <x-forms.section title="Dados da Pendência" />

            <div class="col-md-12">
                <x-forms.input
                    name="title"
                    label="Título *"
                    required
                    :value="old('title', $pendency->title)"
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="description"
                    label="Descrição"
                    rows="4"
                >{{ old('description', $pendency->description) }}</x-forms.textarea>
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="assigned_to"
                    label="Profissional Responsável *"
                    :options="$professionals->pluck('name','id')->toArray()"
                    :value="old('assigned_to', $pendency->assigned_to)"
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
                    :value="old('priority', $pendency->priority)"
                    required
                />
            </div>

            <div class="col-md-3">
                <x-forms.input
                    name="due_date"
                    label="Data de Vencimento"
                    type="date"
                    :value="old('due_date', $pendency->due_date)"
                />
            </div>

            <div class="col-md-12 mt-2">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="is_completed"
                        id="is_completed"
                        value="1"
                        {{ old('is_completed', $pendency->is_completed) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="is_completed">
                        Concluída
                    </label>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pendencies.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-sync me-2"></i> Atualizar
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection
