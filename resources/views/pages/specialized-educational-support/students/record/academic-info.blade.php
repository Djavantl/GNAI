{{-- INFORMAÇÕES ESCOLARES & CURSOS --}}
<section id="informacoes-escolares" class="mb-5  rounded shadow-sm">
    <x-forms.section title="Informações Escolares e Cursos" />
    <div class="pb-3 ps-3 pe-3">
        <div class="row g-3 mt-1">
            <x-show.info-item label="Status Atual" column="col-md-6" isBox="true">
                <span class="{{ $student->status === 'active' ? 'text-success' : 'text-danger' }} fw-bold">
                    <i class="fas fa-circle small me-1"></i> {{ strtoupper($student->status) }}
                </span>
            </x-show.info-item>
            <x-show.info-item label="Data de Ingresso" column="col-md-6" isBox="true">
                {{ $student->entry_date ? \Carbon\Carbon::parse($student->entry_date)->format('d/m/Y') : '---' }}
            </x-show.info-item>
        </div>

        <div class="ms-3 me-3">
            <h6 class="text-title text-uppercase mb-3">Vínculo com Cursos</h6>
            @forelse($student->studentCourses as $courseRelation)
                <div class="card mb-2 border-start border-4 {{ $courseRelation->is_current ? 'border-success' : 'border-secondary' }}">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold ">{{ $courseRelation->course->name ?? 'Curso não identificado' }}</h6>
                                <small class="text-muted">Ano Acadêmico: {{ $courseRelation->academic_year }}</small>
                            </div>
                            @if($courseRelation->is_current)
                                <span class="badge bg-success">ATUAL</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted fw-bold py-5">
                    <i class="fas fa-folder-open d-block mb-2" style="font-size: 2.5rem;"></i>
                    Nenhuma curso ativo do aluno encontrado.
                </div>
            @endforelse
        </div>
         {{-- BOTÃO GERENCIAR --}}
        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
            <x-buttons.link-button
                :href="route('specialized-educational-support.student-courses.history', $student)"
                variant="warning"
                class="btn-sm">
                <i class="fas fa-folder-open"></i> Gerenciar Cursos
            </x-buttons.link-button>
        </div>
    </div>
</section>
