@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => route('specialized-educational-support.teachers.index'),
            $teacher->person->name => route('specialized-educational-support.teachers.show', $teacher),
            'Grade Curricular' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Grade Curricular: {{ $teacher->person->name }}</h2>
            <p class="text-muted">Selecione as disciplinas. O vínculo com os cursos será atualizado automaticamente.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.show', $teacher) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        {{-- Mudamos a rota para a que salva ambos (syncGrade) --}}
        <x-forms.form-card action="{{ route('specialized-educational-support.teachers.disciplines.update', $teacher) }}" method="POST">
            @method('PUT')
            
            <x-forms.section title="Atribuição de Disciplinas por Curso" />

            <div class="col-12">
                @forelse($courses as $course)
                    @if($course->disciplines->count())
                    <div class="border rounded mb-4 overflow-hidden shadow-sm course-card" data-course-id="{{ $course->id }}">

                        {{-- Cabeçalho do Curso --}}
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-light border-bottom">
                            <div>
                                <h6 class="mb-0 fw-bold text-purple-dark text-uppercase">
                                    <i class="fas fa-graduation-cap me-2"></i>{{ $course->name }}
                                </h6>
                                <small class="text-muted">Selecione as disciplinas deste curso</small>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                {{-- Campo Hidden para enviar o ID do curso se houver disciplinas marcadas --}}
                                <input type="hidden" name="courses[]" value="{{ $course->id }}" class="course-id-input" @disabled(!array_intersect($course->disciplines->pluck('id')->toArray(), $selectedDisciplinesIds))>
                                
                                <x-forms.checkbox
                                    name="check_all_course_{{ $course->id }}"
                                    id="check-all-course-{{ $course->id }}"
                                    label="Marcar Todas"
                                    class="check-all-course"
                                    data-course="{{ $course->id }}"
                                />
                            </div>
                        </div>

                        {{-- Disciplinas --}}
                        <div class="px-4 py-3 bg-white">
                            <div class="row">
                                @foreach($course->disciplines as $discipline)
                                    <div class="col-md-3 mb-2">
                                        <x-forms.checkbox
                                            name="disciplines[]"
                                            :value="$discipline->id"
                                            :id="'discipline-'.$discipline->id"
                                            class="discipline-checkbox"
                                            data-course="{{ $course->id }}"
                                            :checked="in_array($discipline->id, old('disciplines', $selectedDisciplinesIds))"
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
                    <i class="fas fa-save"></i> Salvar Grade Curricular
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
   <script>
    document.addEventListener('DOMContentLoaded', function () {
        
        function updateCourseInput(courseId) {
            const card = document.querySelector(`.course-card[data-course-id="${courseId}"]`);
            const hiddenInput = card.querySelector('.course-id-input');
            const anyChecked = card.querySelectorAll('.discipline-checkbox input:checked').length > 0;
            
            // Se tiver disciplina marcada, habilita o input hidden do curso para ele ser enviado no array courses[]
            hiddenInput.disabled = !anyChecked;
        }

        document.addEventListener('change', function (e) {
            if (e.target.type !== 'checkbox') return;

            const wrapper = e.target.closest('.custom-checkbox-wrapper');
            if (!wrapper) return;

            // Lógica de "Selecionar Todas" do curso
            if (wrapper.classList.contains('check-all-course')) {
                const courseId = wrapper.dataset.course;
                const checked = e.target.checked;

                document.querySelectorAll(
                    `.discipline-checkbox[data-course="${courseId}"] input`
                ).forEach(cb => {
                    cb.checked = checked;
                });
                updateCourseInput(courseId);
            }

            // Lógica individual da disciplina
            if (wrapper.classList.contains('discipline-checkbox')) {
                const courseId = wrapper.dataset.course;
                updateCourseInput(courseId);
                
                // Atualiza o "Check All" se necessário
                const allCbs = document.querySelectorAll(`.discipline-checkbox[data-course="${courseId}"] input`);
                const allChecked = Array.from(allCbs).every(c => c.checked);
                const checkAll = document.querySelector(`#check-all-course-${courseId} input`);
                if(checkAll) checkAll.checked = allChecked;
            }
        });
    });
    </script>
    @endpush
@endsection