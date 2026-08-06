@extends('layouts.app')

@section('content')

    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Alunos' => route('specialized-educational-support.students.index'),
            $student->person->name => route('specialized-educational-support.students.show', $student),
            'Matrículas' => route('specialized-educational-support.student-courses.history', $student),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3 align-items-center">
        <div>
            <h2 class="text-title">Nova Matrícula</h2>
            <p class="text-muted">Vincule o aluno a um curso ou série.</p>
        </div>

        <x-buttons.link-button 
            href="{{ route('specialized-educational-support.student-courses.history', $student) }}" 
            variant="secondary"
            aria-label="Cancelar criação de matrícula">
            <i class="fas fa-times" aria-hidden="true"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card 
            action="{{ route('specialized-educational-support.student-courses.store', $student) }}" 
            method="POST">

            <x-forms.section title="Aluno" />

            <x-show.info-item label="Nome do Aluno" column="col-md-6" isBox="true">
                <span class="fw-bold">
                    {{ $student->person->name }}
                </span>
            </x-show.info-item>

            <x-show.info-item label="Matrícula" column="col-md-6" isBox="true">
                {{ $student->registration ?? '—' }}
            </x-show.info-item>

            <x-forms.section title="Dados da Matrícula" />

            <div class="col-md-6">
                <x-forms.select 
                    name="course_id" 
                    label="Curso / Série " 
                    required
                    aria-label="Selecionar curso ou série"
                    :options="$courses->pluck('name', 'id')->toArray()" 
                    :value="old('course_id')" />
            </div>

            <div class="col-md-6">
                <x-forms.input 
                    name="academic_year" 
                    label="Ano Letivo " 
                    type="number" 
                    required
                    aria-label="Ano letivo"
                    :value="old('academic_year', date('Y'))" />
            </div>

            <div class="col-md-6">
                <input type="hidden" name="is_current" value="{{ $mustBeCurrent ? 1 : 0 }}">
                <x-forms.checkbox
                    name="is_current"
                    label="Definir este como o curso atual do aluno"
                    :checked="$mustBeCurrent || filter_var(old('is_current', false), FILTER_VALIDATE_BOOLEAN)"
                    :disabled="$mustBeCurrent"
                    :description="$mustBeCurrent
                        ? 'Este será o primeiro curso do aluno e precisa ser definido como atual.'
                        : 'Ao marcar, o curso atual anterior será movido para o histórico.'"
                />
            </div>

            <div id="academic-follow-up-fields" class="row g-3 m-0 p-0">
                <x-forms.section title="Acompanhamento Acadêmico do Curso Atual" />

                <div class="col-md-12">
                    <x-forms.textarea
                        name="school_attendance_status"
                        label="Situação da Frequência Escolar"
                        rows="3"
                        placeholder="Descreva como está a frequência escolar do aluno neste curso..."
                        :value="old('school_attendance_status')"
                    />
                </div>

                @foreach($courses as $course)
                    <div class="col-12 course-discipline-fields d-none" data-course-id="{{ $course->id }}">
                        <div class="row g-3">
                            @foreach([
                                ['name' => 'failed_discipline_ids', 'title' => 'Disciplinas com Reprovação', 'color' => 'danger'],
                                ['name' => 'at_risk_discipline_ids', 'title' => 'Disciplinas com Risco de Insucesso Acadêmico', 'color' => 'warning'],
                            ] as $group)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <h6 class="fw-bold text-{{ $group['color'] }} mb-3">{{ $group['title'] }}</h6>
                                        @forelse($course->disciplines as $discipline)
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="{{ $group['name'] }}[]"
                                                    value="{{ $discipline->id }}"
                                                    id="{{ $group['name'] }}_{{ $course->id }}_{{ $discipline->id }}"
                                                    {{ in_array((int) $discipline->id, array_map('intval', (array) old($group['name'], [])), true) ? 'checked' : '' }}
                                                    disabled
                                                >
                                                <label class="form-check-label" for="{{ $group['name'] }}_{{ $course->id }}_{{ $discipline->id }}">
                                                    {{ $discipline->name }}
                                                </label>
                                            </div>
                                        @empty
                                            <span class="text-muted">Nenhuma disciplina vinculada ao curso.</span>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-12 d-flex flex-wrap justify-content-end gap-2 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button 
                    href="{{ route('specialized-educational-support.student-courses.history', $student) }}" 
                    variant="secondary"
                    aria-label="Cancelar e voltar ao prontuário">
                    <i class="fas fa-times" aria-hidden="true"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button 
                    type="submit"
                    aria-label="Efetivar matrícula do aluno">
                    <i class="fas fa-save" aria-hidden="true"></i> Salvar
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const courseSelect = document.getElementById('course_id');
            const currentCheckbox = document.getElementById('is_current');
            const followUpFields = document.getElementById('academic-follow-up-fields');

            const updateAcademicFields = () => {
                const isCurrent = currentCheckbox?.checked ?? false;
                followUpFields?.classList.toggle('d-none', !isCurrent);

                document.querySelectorAll('.course-discipline-fields').forEach((container) => {
                    const visible = isCurrent && container.dataset.courseId === courseSelect?.value;
                    container.classList.toggle('d-none', !visible);
                    container.querySelectorAll('input').forEach((input) => input.disabled = !visible);
                });
            };

            courseSelect?.addEventListener('change', updateAcademicFields);
            currentCheckbox?.addEventListener('change', updateAcademicFields);
            updateAcademicFields();
        });
    </script>
@endpush
