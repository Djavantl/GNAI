@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            'Sessão' => route('specialized-educational-support.sessions.show', $session),
            'Cadastrar' => null
        ]" />
    </div>
 
    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Novo Registro de Sessão</h2>
            <p class="text-muted">Sessão #{{ $session->id }} </p>
        </div>
        <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.show', $session) }}" variant="secondary">
            <i class="fas fa-times"></i> Cancelar
        </x-buttons.link-button>
    </div>

    <x-forms.form-card action="{{ route('specialized-educational-support.session-records.store') }}" method="POST">
        <input type="hidden" name="attendance_session_id" value="{{ $session->id }}">

        {{-- SEÇÃO 1: DADOS GERAIS DA SESSÃO --}}
        <x-forms.section title="Informações Gerais da Execução" />
        
        <div class="col-md-6">
            <x-forms.input 
                name="duration" 
                label="Duração" 
                placeholder="Ex: 50 minutos ou 01:00" 
                required 
                :value="old('duration')" 
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea 
                name="activities_performed" 
                label="Atividades Planejadas/Realizadas" 
                rows="3" 
                placeholder="Descreva as atividades realizadas durante a sessão..." 
                required 
                :value="old('activities_performed')" 
            />
        </div>

        <div class="col-md-6">
            <x-forms.textarea 
                name="strategies_used" 
                label="Estratégias Planejadas/Utilizadas" 
                rows="2" 
                placeholder="Métodos, abordagens pedagógicas ou intervenções utilizadas..." 
                :value="old('strategies_used')" 
            />
        </div>

        <div class="col-md-6">
            <x-forms.textarea 
                name="resources_used" 
                label="Recursos Planejadas/Utilizados" 
                rows="2" 
                placeholder="Materiais, tecnologias ou instrumentos utilizados..." 
                :value="old('resources_used')" 
            />
        </div>

        <div class="col-md-12">
            <x-forms.textarea 
                name="general_observations" 
                label="Observações Gerais" 
                rows="2" 
                placeholder="Observações relevantes sobre a sessão..." 
                :value="old('general_observations')" 
            />
        </div>

        {{-- SEÇÃO 2: AVALIAÇÕES INDIVIDUAIS --}}
        <x-forms.section title="Avaliação Individual por Aluno" />
        
        <div class="col-12">
            <div class="row g-0 border rounded overflow-hidden">
                <div class="col-md-3 bg-light border-end">
                    <div class="list-group list-group-flush" id="students-list" role="tablist">
                        @foreach($session->students as $index => $student)
                            <button type="button" 
                                class="list-group-item list-group-item-action @if($loop->first) active @endif d-flex justify-content-between align-items-center" 
                                data-bs-toggle="list" 
                                data-bs-target="#student-eval-{{ $index }}"
                                id="tab-{{ $index }}">
                                {{ $student->person->name }}
                                <i class="fas fa-chevron-right small opacity-50"></i>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-9 p-4 bg-white">
                    <div class="tab-content">
                        @foreach($session->students as $index => $student)
                            <div class="tab-pane fade @if($loop->first) show active @endif" id="student-eval-{{ $index }}">
                                <h5 class="mb-4 border-bottom pb-2 text-title">Avaliação: {{ $student->person->name }}</h5>
                                <input type="hidden" name="evaluations[{{ $index }}][student_id]" value="{{ $student->id }}">

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-check form-switch p-3 bg-light rounded border mb-3">
                                            <input class="form-check-input ms-0 me-2 presence-toggle" type="checkbox" 
                                                name="evaluations[{{ $index }}][is_present]" 
                                                id="presence_{{ $index }}" 
                                                value="1" checked>
                                            <label class="form-label fw-bold text-purple-dark" for="presence_{{ $index }}">Aluno Presente</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12" id="absence_fields_{{ $index }}" style="display: none;">
                                        <x-forms.textarea 
                                            name="evaluations[{{ $index }}][absence_reason]" 
                                            label="Motivo da Ausência" 
                                            rows="2" 
                                            placeholder="Informe o motivo da ausência do aluno..." 
                                            :value="old('evaluations.'.$index.'.absence_reason')"
                                        />
                                    </div>

                                    <div class="col-md-12 evaluation-fields" id="eval_fields_{{ $index }}">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <x-forms.textarea 
                                                    name="evaluations[{{ $index }}][student_participation]" 
                                                    label="Participação" 
                                                    rows="3" 
                                                    placeholder="Descreva o nível de participação do aluno..." 
                                                    required 
                                                />
                                            </div>
                                            <div class="col-md-12">
                                                <x-forms.textarea 
                                                    name="evaluations[{{ $index }}][adaptations_made]" 
                                                    label="Adaptações para este Aluno" 
                                                    rows="3" 
                                                    placeholder="Adaptações realizadas durante a sessão..." 
                                                />
                                            </div>
                                            <div class="col-md-12">
                                                <x-forms.textarea 
                                                    name="evaluations[{{ $index }}][development_evaluation]" 
                                                    label="Avaliação do Desenvolvimento" 
                                                    rows="3" 
                                                    placeholder="Avaliação do desempenho e evolução do aluno..." 
                                                    required 
                                                />
                                            </div>
                                            <div class="col-md-12">
                                                <x-forms.textarea 
                                                    name="evaluations[{{ $index }}][progress_indicators]" 
                                                    label="Indicadores de Progresso" 
                                                    rows="3" 
                                                    placeholder="Evidências observáveis de progresso..." 
                                                />
                                            </div>
                                            <div class="col-md-6">
                                                <x-forms.textarea 
                                                    name="evaluations[{{ $index }}][recommendations]" 
                                                    label="Recomendações" 
                                                    rows="3" 
                                                    placeholder="Sugestões para continuidade do trabalho..." 
                                                />
                                            </div>
                                            <div class="col-md-6">
                                                <x-forms.textarea 
                                                    name="evaluations[{{ $index }}][next_session_adjustments]" 
                                                    label="Ajustes para Próxima Sessão" 
                                                    rows="3" 
                                                    placeholder="O que ajustar ou manter para a próxima sessão..." 
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4 mt-4">
            <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.show', $session) }}" variant="secondary">
                <i class="fas fa-times"></i> Cancelar
            </x-buttons.link-button>
            <x-buttons.submit-button type="submit" class="btn-action new submit">
                <i class="fas fa-save"></i> Salvar
            </x-buttons.submit-button>
        </div>
    </x-forms.form-card>
@endsection

@push('scripts')
    @vite(['resources/js/pages/specialized-educational-support/session-record-create.js'])
@endpush