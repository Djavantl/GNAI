{{-- DEFICIÊNCIAS --}}
<section id="deficiencias" class="mb-5 bg-soft-info rounded shadow-sm">
    <x-forms.section title="Deficiências" />
    <div class="pb-3 ps-3 pe-3">
        {{-- Conteúdo dos Cards/Dados --}}
        <div class="row g-3 mt-2">
            @forelse($student->deficiencies as $def)
                <div class="col-md-6">
                    <div class="card p-3 border-light bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            
                            <div>
                                <strong class="d-block">{{ $def->deficiency->name }}</strong>
                                <span class="small text-muted">
                                    GRAU: {{ $def->severity ?? '—' }}
                                </span>
                            </div>

                            

                        </div>
                    </div>
                </div>
            @empty
                <div class="text-muted ps-3">Nenhuma deficiência registrada.</div>
            @endforelse
        </div>

        {{-- DIV DE AJUSTE DOS BOTÕES (Abaixo de tudo dentro da section) --}}
        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
            <x-buttons.link-button :href="route('specialized-educational-support.student-deficiencies.index', $student)" variant="warning" class="btn-sm">
                <i class="fas fa-folder-open"></i> Gerenciar
            </x-buttons.link-button>

            
        </div>
    </div>
</section>