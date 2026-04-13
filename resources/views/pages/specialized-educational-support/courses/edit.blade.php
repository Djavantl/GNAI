@extends('layouts.master')

@section('title', 'Editar Cursos')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Cursos' => route('specialized-educational-support.courses.index'),
            $course->name => route('specialized-educational-support.courses.show', $course),
            'Editar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Editar o curso {{ $course->name }}</h2>
            <p class="text-muted">Atualize as informações do curso e sua matriz curricular.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.courses.show', $course) }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <x-forms.form-card action="{{ route('specialized-educational-support.courses.update', $course) }}" method="POST">
        @method('PUT')

        <x-forms.section title="Informações Básicas" />
        <div class="col-md-12">
            <x-forms.input name="name" label="Nome do Curso" required :value="old('name', $course->name)" />
        </div>
        <div class="col-md-12">
            <x-forms.textarea name="description" label="Descrição" :value="old('description', $course->description)" />
        </div>

        <x-forms.section title="Matriz Curricular (Selecione as Disciplinas)" />

        <div class="col-md-12 mb-3">
            <label for="discipline-search" class="form-label fw-bold text-purple-dark">
                Buscar disciplina
            </label>
            <input
                type="text"
                id="discipline-search"
                class="form-control"
                placeholder="Digite o nome da disciplina..."
                autocomplete="off"
            >
        </div>

        <div class="col-md-12">
            <div class="row px-3" id="discipline-list">
                @foreach($disciplines as $discipline)
                    <div class="col-md-4 mb-2 discipline-item">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="discipline_ids[]"
                                value="{{ $discipline->id }}"
                                id="disc_{{ $discipline->id }}"
                                {{ $course->disciplines->contains('id', $discipline->id) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="disc_{{ $discipline->id }}">
                                {{ $discipline->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="discipline-empty" class="text-muted small d-none mt-2 px-3">
                Nenhuma disciplina encontrada com esse termo.
            </div>
        </div>

        <div class="col-md-6">
            <x-forms.select
                name="is_active"
                label="Status do Curso"
                :options="[1 => 'Ativo', 0 => 'Inativo']"
                :selected="old('is_active', $course->is_active)"
                required
            />
        </div>

        <div class="col-12 d-flex justify-content-end border-t pt-4 px-4 pb-4">
            <x-buttons.link-button class="me-3" href="{{ route('specialized-educational-support.courses.show', $course) }}" variant="secondary">
                <i class="fas fa-times"></i>Cancelar
            </x-buttons.link-button>
            <x-buttons.submit-button type="submit">
                <i class="fas fa-save"></i>Salvar
            </x-buttons.submit-button>
        </div>
    </x-forms.form-card>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('discipline-search');
            const items = document.querySelectorAll('.discipline-item');
            const emptyMessage = document.getElementById('discipline-empty');

            function filterDisciplines() {
                const term = searchInput.value.toLowerCase().trim();
                let visibleCount = 0;

                items.forEach(item => {
                    const label = item.querySelector('label')?.textContent.toLowerCase() || '';
                    const match = label.includes(term);

                    item.style.display = match ? '' : 'none';

                    if (match) visibleCount++;
                });

                emptyMessage.classList.toggle('d-none', visibleCount > 0);
            }

            searchInput.addEventListener('input', filterDisciplines);
        });
    </script>
@endpush