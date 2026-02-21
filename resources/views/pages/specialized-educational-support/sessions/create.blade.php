@extends('layouts.master')

@section('title', 'Agendar Nova Sessão')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Sessões' => route('specialized-educational-support.sessions.index'),
            'Cadastrar' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Agendar Nova Sessão</h2>
            <p class="text-muted">Preencha os dados para agendar o atendimento especializado.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.sessions.store') }}" method="POST">
            
            <x-forms.section title="Participantes e Horário" />

             {{-- Tipo de Atendimento --}}
            <div class="col-md-6">
                <x-forms.select
                    name="type"
                    label="Tipo de Atendimento *"
                    required
                    :options="['individual' => 'Individual', 'group' => 'Em Grupo']"
                    :value="old('type', 'individual')"
                    id="session_type"
                />
            </div>

            <div class="col-md-6">
                <x-forms.select
                    name="professional_id"
                    label="Profissional"
                    required
                    :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person->name ?? 'Sem Nome'])"
                    search="true"
                />
            </div>

            {{-- Container de Alunos Dinâmico --}}
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold text-purple-dark">Alunos Participantes *</label>
                <div id="students-container" data-students="{{ $students->map(fn($s) => ['id' => $s->id, 'name' => $s->person->name])->toJson() }}"></div>
                <div class="d-flex justify-content-start" >
                <button type="button" id="add-student-btn" class="btn btn-sm btn-success mt-2 mb-3 d-none">
                    <i class="fas fa-plus"></i> Adicionar outro Aluno
                </button>
                </div>
                
            </div>

            <div class="col-md-6">
                <x-forms.input
                    name="session_date"
                    label="Data da Sessão *"
                    type="date"
                    required
                />
            </div>

            <div class="col-md-6">
                <div class="row">
                   <div class="col-6">
                        <x-forms.select 
                            name="start_time" 
                            label="Início *" 
                            required 
                            :options="$startTimes" 
                            :value="old('start_time')" 
                        />
                    </div>
                    <div class="col-6">
                        <x-forms.select 
                            name="end_time" 
                            label="Fim *" 
                            required 
                            :options="$endTimes" 
                            :value="old('end_time', '09:00')" 
                        />
                    </div>
                </div>
            </div>

            <x-forms.section title="Disponibilidade do Dia" />

            <div class="col-md-12 mb-4">
                <div id="schedule" class="border rounded p-3 bg-white shadow-sm" style="overflow-x: auto;">
                    <p class="text-muted text-center py-3">Selecione aluno, profissional e data.</p>
                </div>
            </div>

            
            <x-forms.section title="Detalhes do Atendimento" />

            <div class="col-md-6">
                <x-forms.input name="location" label="Local" :value="old('location')" />
            </div>

            <div class="col-md-12">
                <x-forms.textarea
                    name="session_objective"
                    label="Objetivo da Sessão"
                    rows="3"
                    :value="old('session_objective')"
                />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.sessions.index') }}" variant="secondary">
                    Cancelar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-save mr-2"></i> Salvar Sessão
                </x-buttons.submit-button>
            </div>

        </x-forms.form-card>
    </div>
    
    @push('scripts')
    <script>
        window.routes = {
            sessionAvailability: "{{ route('specialized-educational-support.sessions.availability') }}"
        };

        document.addEventListener('DOMContentLoaded', function () {

            const type = document.getElementById('session_type');
            const single = document.getElementById('single-student-wrapper');
            const group = document.getElementById('group-students-wrapper');

            const status = document.querySelector('[name="status"]');
            const cancelBox = document.getElementById('cancel-reason-wrapper');

            function toggleStudents() {
                if (type.value === 'group') {
                    single.classList.add('d-none');
                    group.classList.remove('d-none');
                } else {
                    single.classList.remove('d-none');
                    group.classList.add('d-none');
                }
            }

            function toggleCancelReason() {
                if (status.value === 'Cancelado') {
                    cancelBox.classList.remove('d-none');
                } else {
                    cancelBox.classList.add('d-none');
                }
            }

            type.addEventListener('change', toggleStudents);
            status.addEventListener('change', toggleCancelReason);

            toggleStudents();
            toggleCancelReason();
        });
        </script>

    @endpush
@endsection