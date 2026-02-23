{{-- DEFICIÊNCIAS --}}
<section id="deficiencias" class="mb-5 bg-soft-info rounded shadow-sm">
    <x-forms.section title="Deficiências" />

    <div class="pb-3 ps-3 pe-3">
        <div class="row g-3 mt-2">
            @php
                $severityLabels = [
                    'mild' => 'Leve',
                    'moderate' => 'Moderada',
                    'severe' => 'Severa'
                ];
            @endphp

            @forelse($student->deficiencies as $def)
                <div class="col-md-6">
                    <div class="card p-3 border-light bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block">
                                    {{ $def->name ?? 'Deficiência não encontrada' }}
                                </strong>

                                <span class="small text-muted">
                                    GRAU:
                                    {{ $def->pivot->severity ? $severityLabels[$def->pivot->severity] : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-muted ps-3">
                    Nenhuma deficiência registrada.
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
            <x-buttons.link-button
                :href="route('specialized-educational-support.student-deficiencies.index', $student)"
                variant="warning"
                class="btn-sm"
            >
                <i class="fas fa-folder-open"></i> Gerenciar Deficiências
            </x-buttons.link-button>
        </div>
    </div>
</section>