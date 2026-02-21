<section id="guardians" class="mb-5 bg-soft-info rounded shadow-sm">

    <x-forms.section title="Responsáveis" class="m-0" />

    <div class="pb-3 ps-3 pe-3">
        <div class="row g-3">
            @forelse($student->guardians as $g)
                <div class="col-md-12">
                    <div class="card border-0 bg-white shadow-sm">
                        <div class="card-body">

                            <div class="row align-items-center">

                                <div class="col-md-4 d-flex align-items-center">
                                    <img
                                        src="{{ $g->person->photo_url }}"
                                        class="rounded-circle me-3"
                                        style="width:48px;height:48px;object-fit:cover;"
                                    >
                                    <strong>{{ $g->person->name }}</strong>
                                </div>

                                {{-- RELAÇÃO --}}
                                <div class="col-md-2 text-muted small">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $g->relationship ?? '---' }}
                                </div>

                                {{-- TELEFONE --}}
                                <div class="col-md-3 text-muted small">
                                    <i class="fas fa-phone me-1"></i>
                                    {{ $g->person->phone ?? '---' }}
                                </div>

                                {{-- EMAIL --}}
                                <div class="col-md-3 text-muted small">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{ $g->person->email ?? '---' }}
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border border-dashed text-center">
                        Sem responsáveis registrados.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
            <x-buttons.link-button
                :href="route('specialized-educational-support.guardians.index', $student)"
                variant="warning"
                class="btn-sm">
                <i class="fas fa-folder-open"></i> Gerenciar
            </x-buttons.link-button>
        </div>
    </div>
</section>
