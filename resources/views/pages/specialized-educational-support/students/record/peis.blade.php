 {{-- PEIs --}}
                    <section id="peis" class="mb-5 bg-soft-info rounded shadow-sm">

                        <x-forms.section title="PEIs (Plano de Ensino Individualizado)" class="m-0" />
                        <div class="pb-3 ps-3 pe-3">
                            <div class="row g-3">
                                @forelse($student->peis as $pei)
                                    <div class="col-md-12">
                                        <div class="card border-0 shadow-sm hover-shadow transition-all">
                                            <div class="card-body d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($pei->is_finished)
                                                            <i class="fas fa-check-circle text-success fa-2x"></i>
                                                        @else
                                                            <i class="fas fa-clock text-warning fa-2x"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 fw-bold">PEI - {{ $pei->course->name ?? 'Geral' }}</h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-user-tie me-1"></i> Prof: {{ $pei->teacher_name ?? 'Não informado' }} | 
                                                            <i class="fas fa-calendar me-1"></i> {{ $pei->created_at->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <x-buttons.link-button :href="route('specialized-educational-support.pei.show', $pei->id)" variant="info" class="btn-sm">
                                                    <i class="fas fa-eye me-1"></i> Abrir Detalhes
                                                </x-buttons.link-button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12"><div class="alert alert-light border border-dashed text-center">Nenhum PEI encontrado para este aluno.</div></div>
                                @endforelse
                            </div>
                            {{-- DIV DE AJUSTE DOS BOTÕES (Abaixo de tudo dentro da section) --}}
                            <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                                <x-buttons.link-button :href="route('specialized-educational-support.pei.index', $student)" variant="warning" class="btn-sm">
                                    <i class="fas fa-edit"></i> Editar PEIs
                                </x-buttons.link-button>
                            </div>
                        </div>
                    </section>