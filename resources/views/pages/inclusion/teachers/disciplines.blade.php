@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => route('specialized-educational-support.teachers.index'),
            $teacher->person->name => route('specialized-educational-support.teachers.show', $teacher),
            'Matriz Curricular' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Matriz Curricular: {{ $teacher->person->name }}</h2>
            <p class="text-muted">Selecione as disciplinas por curso. Cada curso será salvo separadamente.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.show', $teacher) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.teachers.disciplines.update', $teacher) }}" method="POST">
            @method('PUT')

            <x-forms.section title="Atribuição de Disciplinas por Curso" />

            <div class="col-12">
                @forelse($courses as $course)
                    @if($course->disciplines->count())
                        <div class="border rounded mb-4 overflow-hidden shadow-sm course-card" data-course-id="{{ $course->id }}">
                            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light border-bottom">
                                <div>
                                    <h6 class="mb-0 fw-bold text-purple-dark text-uppercase">
                                        <i class="fas fa-graduation-cap me-2"></i>{{ $course->name }}
                                    </h6>
                                    <small class="text-muted">Selecione as disciplinas deste curso</small>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <x-forms.checkbox
                                        name="check_all_course_{{ $course->id }}"
                                        id="check-all-course-{{ $course->id }}"
                                        label="Marcar Todas"
                                        class="check-all-course"
                                        data-course="{{ $course->id }}"
                                    />
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-white">
                                <div class="row">
                                    @foreach($course->disciplines as $discipline)
                                        <div class="col-md-3 mb-2">
                                            <x-forms.checkbox
                                                name="assignments[{{ $course->id }}][]"
                                                :value="$discipline->id"
                                                :id="'discipline-'.$course->id.'-'.$discipline->id"
                                                class="discipline-checkbox"
                                                data-course="{{ $course->id }}"
                                                :checked="in_array($discipline->id, $selectedByCourse[$course->id] ?? [])"
                                                :label="$discipline->name"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="alert alert-warning text-center">
                        Nenhum curso ou disciplina disponível para atribuição.
                    </div>
                @endforelse
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 bg-light mt-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.show', $teacher) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Salvar Matriz Curricular
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                function updateCourseState(courseId) {
                    const card = document.querySelector(`.course-card[data-course-id="${courseId}"]`);
                    if (!card) return;

                    const checkAllWrapper = card.querySelector('.check-all-course');
                    const disciplineInputs = card.querySelectorAll('.discipline-checkbox input');
                    const checkedCount = Array.from(disciplineInputs).filter(cb => cb.checked).length;

                    if (checkAllWrapper) {
                        const checkAllInput = checkAllWrapper.querySelector('input');
                        if (checkAllInput) {
                            checkAllInput.checked = checkedCount > 0 && checkedCount === disciplineInputs.length;
                        }
                    }
                }

                document.addEventListener('change', function (e) {
                    if (e.target.type !== 'checkbox') return;

                    const wrapper = e.target.closest('.custom-checkbox-wrapper');
                    if (!wrapper) return;

                    if (wrapper.classList.contains('check-all-course')) {
                        const courseId = wrapper.dataset.course;
                        const checked = e.target.checked;

                        document.querySelectorAll(`.discipline-checkbox[data-course="${courseId}"] input`).forEach(cb => {
                            cb.checked = checked;
                        });

                        updateCourseState(courseId);
                    }

                    if (wrapper.classList.contains('discipline-checkbox')) {
                        const courseId = wrapper.dataset.course;
                        updateCourseState(courseId);
                    }
                });
            });
        </script>
    @endpush
@endsection