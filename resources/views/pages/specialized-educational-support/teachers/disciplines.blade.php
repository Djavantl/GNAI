@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Professores' => route('specialized-educational-support.teachers.index'),
            $teacher->person->name => route('specialized-educational-support.teachers.show', $teacher),
            'Disciplinas' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Disciplinas de {{ $teacher->person->name }}</h2>
            <p class="text-muted">Selecione as disciplinas que este professor leciona na instituição.</p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.show', $teacher) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.teachers.disciplines.update', $teacher) }}" method="POST">
            @method('PUT')
            
            <x-forms.section title="Grade Curricular do Professor" />

            <div class="col-12">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Dica:</strong> Você pode selecionar disciplinas por curso inteiro.
                </div>

                @forelse($courses as $course)
                    @if($course->disciplines->count())
                    <div class="border rounded mb-4 overflow-hidden shadow-sm">

                        {{-- Cabeçalho do Curso --}}
                        <div class="d-flex justify-content-between align-items-center px-4 py-3"
                            style="background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
                            <h6 class="mb-0 fw-bold text-purple-dark">
                                <i class="fas fa-folder me-2"></i>{{ $course->name }}
                            </h6>

                            <x-forms.checkbox
                                name="check_all_course_{{ $course->id }}"
                                id="check-all-course-{{ $course->id }}"
                                label="Selecionar Curso"
                                class="check-all-course"
                                data-course="{{ $course->id }}"
                            />
                        </div>

                        {{-- Disciplinas do Curso --}}
                        <div class="px-4 py-3 bg-soft-info">
                            <div class="row">
                                @foreach($course->disciplines as $discipline)
                                    <div class="col-md-3 mb-3">
                                        <x-forms.checkbox
                                            name="disciplines[]"
                                            :value="$discipline->id"
                                            :id="'discipline-'.$discipline->id"
                                            class="discipline-checkbox"
                                            data-course="{{ $course->id }}"
                                            :checked="in_array($discipline->id, old('disciplines', $selectedDisciplinesIds))"
                                            :label="$discipline->name"
                                        />

                                        @if($discipline->description)
                                            <small class="text-muted d-block ms-4">
                                                {{ Str::limit($discipline->description, 40) }}
                                            </small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    @endif
                @empty
                    <div class="text-center py-4">
                        <p class="text-muted">Nenhum curso com disciplinas ativas encontrado.</p>
                    </div>
                @endforelse
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 bg-light mt-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.teachers.show', $teacher) }}" variant="secondary">
                    <i class="fas fa-times"></i> Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit">
                    <i class="fas fa-save"></i> Atualizar Grade do Professor
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>

    @push('scripts')
   <script>
    document.addEventListener('DOMContentLoaded', function () {

        document.addEventListener('change', function (e) {
            if (e.target.type !== 'checkbox') return;

            const wrapper = e.target.closest('.custom-checkbox-wrapper');
            if (!wrapper) return;

            if (wrapper.classList.contains('check-all-course')) {
                const courseId = wrapper.dataset.course;
                const checked = e.target.checked;

                document.querySelectorAll(
                    '.discipline-checkbox[data-course="'+courseId+'"] input'
                ).forEach(cb => cb.checked = checked);
            }
        });

    });
    </script>
    @endpush
@endsection