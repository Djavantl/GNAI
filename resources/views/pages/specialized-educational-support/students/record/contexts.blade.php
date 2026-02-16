<section id="contexts" class="mb-5 bg-soft-info rounded shadow-sm">

    <x-forms.section title="Contexto Atual" class="m-0" />

    <div class="pb-3 ps-3 pe-3">

        @if($student->currentContext !== null)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-brain me-2"></i>
                        Contexto
                    </h6>

                    <span class="badge bg-success">Atual</span>
                </div>

                <div class="card-body">
                    <div class="row g-3 align-items-center">

                        <div class="col-md-4">
                            <small class="text-muted d-block">Semestre</small>
                            <strong>
                                {{ $student->currentContext->semester->label ?? '---' }}
                            </strong>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted d-block">Criado por</small>
                            <strong>
                                {{ $student->currentContext->evaluator->person->name ?? '---' }}
                            </strong>
                        </div>

                        <div class="col-md-4 text-md-end">
                            <x-buttons.link-button
                                :href="route('specialized-educational-support.student-context.show', [$student->currentContext->id])"
                                variant="info"
                                class="btn-sm">
                                <i class="fas fa-eye me-1"></i> Abrir Detalhes
                            </x-buttons.link-button>
                        </div>

                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-light border border-dashed text-center">
                No current evaluation context found.
            </div>
        @endif

        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
            <x-buttons.link-button
                :href="route('specialized-educational-support.student-context.index', $student)"
                variant="warning"
                class="btn-sm">
                <i class="fas fa-edit"></i> Edit Contexts
            </x-buttons.link-button>
        </div>

    </div>
</section>
