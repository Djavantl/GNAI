@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            'PEI' => route('specialized-educational-support.pei.show', $pei),
            'Cadastrar Adaptação' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Cadastrar Adaptação Curricular</h2>
            <p class="text-muted">Defina os objetivos, conteúdos e metodologias adaptadas para este PEI.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei) }}" variant="secondary">
            <i class="fas fa-times"></i>Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.pei-discipline.store', $pei) }}" method="POST">
            @csrf
            <x-forms.section title="Identificação da Disciplina" />

            <div class="col-md-6">
                @if(auth()->user()->teacher_id)

                <input type="hidden" name="teacher_id" value="{{ auth()->user()->teacher_id }}">

                <x-forms.input
                    name="teacher_name"
                    label="Professor Responsável"
                    value="{{ auth()->user()->teacher->person->name }}"
                    disabled
                />

                @else
                <x-forms.select
                    id="teacher-select"
                    name="teacher_id"
                    label="Professor Responsável"
                    :options="$teachers->pluck('person.name', 'id')->toArray()"
                    :value="old('teacher_id')"
                    required
                />
                @endif
            </div>

            <div class="col-md-6">
                <x-forms.select
                    id="discipline-select"
                    name="discipline_id"
                    label="Disciplina"
                    :options="[]"
                    :value="old('discipline_id')"
                    required
                />
            </div>

            <x-forms.section title="Planejamento Adaptado" />

            <div class="col-md-12">
                <x-forms.textarea
                    name="specific_objectives"
                    label="Objetivos Específicos"
                    :required="true"
                    rows="4"
                    :value="old('specific_objectives')"
                    placeholder="Descreva os objetivos de aprendizagem adaptados para o aluno..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="content_programmatic"
                    label="Conteúdo Programático"
                    rows="4"
                    :required="true"
                    :value="old('content_programmatic')"
                    placeholder="Liste os conteúdos que serão abordados nesta disciplina..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="methodologies"
                    label="Metodologias e Estratégias"
                    rows="4"
                    :required="true"
                    :value="old('methodologies')"
                    placeholder="Descreva como o conteúdo será ensinado (recursos, materiais, apoios)..."
                />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="evaluations"
                    label="Processo de Avaliação"
                    rows="4"
                    :required="true"
                    :value="old('evaluations')"
                    placeholder="Como a aprendizagem será avaliada nesta disciplina?"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.pei.show', $pei) }}" variant="secondary">
                    <i class="fas fa-times"></i>Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i>Salvar Adaptação
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const teacherSelect = document.getElementById('teacher-select');
    const disciplineSelect = document.getElementById('discipline-select');
    const form = document.querySelector('form');

    const urlBase = "{{ route('specialized-educational-support.teacher-disciplines', $pei) }}";
    const loggedTeacherId = {{ auth()->user()->teacher_id ?? 'null' }};

    function resetDisciplines() {
        disciplineSelect.innerHTML = '<option value="">Selecione uma disciplina</option>';
        disciplineSelect.disabled = true;
    }

    function populateDisciplines(items, selectedId = null) {

        disciplineSelect.innerHTML = '';

        const emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.text = 'Selecione uma disciplina';
        disciplineSelect.appendChild(emptyOpt);

        items.forEach(function (d) {

            const opt = document.createElement('option');
            opt.value = d.id;
            opt.text = d.name;

            if (selectedId && String(d.id) === String(selectedId)) {
                opt.selected = true;
            }

            disciplineSelect.appendChild(opt);
        });

        disciplineSelect.disabled = false;
    }

    function loadDisciplines(teacherId) {

        if (!teacherId) {
            resetDisciplines();
            return;
        }

        const url = new URL(urlBase, window.location.origin);
        url.searchParams.set('teacher_id', teacherId);

        fetch(url.toString(), {
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(res => res.json())
        .then(data => {
            populateDisciplines(data, "{{ old('discipline_id') }}");
        })
        .catch(err => {
            console.error(err);
            alert('Erro ao buscar disciplinas do professor.');
            resetDisciplines();
        });
    }

    // estado inicial
    resetDisciplines();

    // gestor/admin
    if (teacherSelect) {

        teacherSelect.addEventListener('change', function () {
            loadDisciplines(this.value);
        });

        if (teacherSelect.value) {
            loadDisciplines(teacherSelect.value);
        }
    }

    // professor logado
    if (!teacherSelect && loggedTeacherId) {
        loadDisciplines(loggedTeacherId);
    }

    // garantir que disabled não impeça o submit
    if (form) {
        form.addEventListener('submit', function () {
            disciplineSelect.disabled = false;
        });
    }

});
</script>
@endpush
@endsection