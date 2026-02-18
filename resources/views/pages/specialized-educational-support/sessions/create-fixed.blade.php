@extends('layouts.master')

@section('content')
    <div class="mb-5">
        <x-breadcrumb :items="[
            'Home' => route('dashboard'),
            'Prontuário' => route('specialized-educational-support.students.show', $student),
            'Sessões' => route('specialized-educational-support.students.sessions.index', $student),
            'Novo Agendamento' => null
        ]" />
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div>
            <h2 class="text-title">Agendar para {{ $student->person->name }}</h2>
            <p class="text-muted">Sessão individual por padrão. Mude para "Em Grupo" para adicionar participantes.</p>
        </div>
    </div>

    <div class="mt-3">
        <x-forms.form-card action="{{ route('specialized-educational-support.sessions.store') }}" method="POST">
            
            <x-forms.section title="Participantes e Horário" />

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
                    label="Profissional *"
                    required
                    :options="$professionals->mapWithKeys(fn($p) => [$p->id => $p->person->name])"
                    :value="old('professional_id')"
                />
            </div>

            {{-- Alunos Participantes --}}
            <div class="col-md-12 mb-4">
                <label class="form-label fw-bold text-purple-dark">Alunos Participantes</label>

                <div id="students-container" 
                     data-students="{{ $students->map(fn($s) => ['id' => $s->id, 'name' => $s->person->name])->toJson() }}">
                    
                    {{-- Aluno Fixo (Imutável) --}}
                    <div class="d-flex align-items-center gap-2 mb-2 student-row">
                        <div class="flex-grow-1">
                            <div class="form-control bg-light d-flex align-items-center" style="height: 45px;">
                                <i class="fas fa-user-lock me-2 text-muted"></i>
                                <strong>{{ $student->person->name }}</strong>
                                <span class="badge bg-secondary ms-2">Aluno Principal</span>
                            </div>
                            <input type="hidden" name="student_ids[]" class="student-select-item" value="{{ $student->id }}">
                        </div>
                        <div style="width: 45px"></div> {{-- Espaço para alinhar com lixeiras futuras --}}
                    </div>
                </div>

                <button type="button" id="add-student-btn" class="btn btn-sm btn-outline-success mt-2 d-none">
                    <i class="fas fa-plus"></i> Adicionar Participante ao Grupo
                </button>
            </div>

            <div class="col-md-6">
                <x-forms.input name="session_date"  label="Data da Sessão *" type="date" required :value="old('session_date')" />
            </div>

            <div class="col-md-6">
                <div class="row">
                   <div class="col-6">
                        <x-forms.select name="start_time" label="Início *" required :options="$startTimes" :value="old('start_time')" />
                    </div>
                    <div class="col-6">
                        <x-forms.select name="end_time" label="Fim *" required :options="$endTimes" :value="old('end_time')" />
                    </div>
                </div>
            </div>

            <x-forms.section title="Disponibilidade do Dia" />
            <div class="col-md-12 mb-4">
                <div id="schedule" class="border rounded p-3 bg-white shadow-sm" style="overflow-x: auto; min-height: 100px;">
                    <p class="text-muted text-center py-3">Selecione data e profissional para ver a agenda.</p>
                </div>
            </div>

            <x-forms.section title="Detalhes Adicionais" />
            <div class="col-md-12 mb-3">
                <x-forms.input name="location" label="Local" :value="old('location')" />
            </div>
            <div class="col-md-12">
                <x-forms.textarea name="session_objective" label="Objetivo da Sessão" rows="3" :value="old('session_objective')" />
            </div>

            <div class="col-12 d-flex justify-content-end gap-3 border-t pt-4 px-4 pb-4">
                <x-buttons.link-button href="{{ route('specialized-educational-support.students.sessions.index', $student) }}" variant="secondary">
                    Voltar
                </x-buttons.link-button>

                <x-buttons.submit-button type="submit" class="btn-action new submit px-5">
                    <i class="fas fa-calendar-check mr-2"></i> Confirmar Agendamento
                </x-buttons.submit-button>
            </div>
        </x-forms.form-card>
    </div>
@endsection


@push('scripts')
<script>
(function() {
    // Usamos um timer curto para garantir que rodamos DEPOIS do script do app.js
    setTimeout(() => {
        const container = document.getElementById('students-container');
        if (!container) return;

        // 1. "Limpamos" os eventos que o app.js colocou no Select de Tipo
        // para que ele não resete nosso HTML fixo
        const typeSelect = document.getElementById('session_type');
        const newTypeSelect = typeSelect.cloneNode(true);
        typeSelect.parentNode.replaceChild(newTypeSelect, typeSelect);

        const addBtn = document.getElementById('add-student-btn');
        const fixedId = "{{ $student->id }}";
        const fixedName = "{{ $student->person->name }}";
        const studentsList = JSON.parse(container.dataset.students || '[]');

        // 2. REINJETAMOS o Aluno Fixo (corrigindo o que o app.js apagou)
        function initFixedStudent() {
            container.innerHTML = `
                <div class="d-flex align-items-center gap-2 mb-2 student-row fixed-student">
                    <div class="flex-grow-1">
                        <div class="form-control bg-light d-flex align-items-center" style="height: 45px;">
                            <i class="fas fa-user-lock me-2 text-muted"></i>
                            <strong>${fixedName}</strong>
                            <span class="badge bg-secondary ms-2 small">Principal</span>
                        </div>
                        <input type="hidden" name="student_ids[]" class="student-select-item" value="${fixedId}">
                    </div>
                    <div style="width: 45px"></div>
                </div>
            `;
        }

        // 3. Função para adicionar outros alunos (Modo Grupo)
        function addGuestRow() {
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center gap-2 mb-2 student-row guest-student';
            
            const selectedIds = Array.from(document.querySelectorAll('.student-select-item')).map(i => i.value);
            let options = '<option value="">Selecionar aluno...</option>';
            
            studentsList.forEach(s => {
                if (!selectedIds.includes(s.id.toString())) {
                    options += `<option value="${s.id}">${s.name}</option>`;
                }
            });

            div.innerHTML = `
                <div class="flex-grow-1">
                    <select name="student_ids[]" class="form-select student-select-item" required>
                        ${options}
                    </select>
                </div>
                <button type="button" class="btn btn-outline-danger remove-guest-btn" style="width: 45px">
                    <i class="fas fa-trash"></i>
                </button>
            `;

            div.querySelector('.remove-guest-btn').onclick = () => {
                div.remove();
                if (typeof loadSchedule === 'function') loadSchedule();
            };

            div.querySelector('select').onchange = () => {
                if (typeof loadSchedule === 'function') loadSchedule();
            };

            container.appendChild(div);
        }

        // 4. Nova lógica de alternância (que preserva o fixo)
        function handleTypeChange() {
            if (newTypeSelect.value === 'group') {
                addBtn.classList.remove('d-none');
            } else {
                addBtn.classList.add('d-none');
                // Remove apenas os convidados
                container.querySelectorAll('.guest-student').forEach(el => el.remove());
                if (typeof loadSchedule === 'function') loadSchedule();
            }
        }

        // --- EXECUÇÃO ---
        initFixedStudent();
        
        newTypeSelect.addEventListener('change', handleTypeChange);
        addBtn.onclick = addGuestRow;

        // Garante que o estado inicial esteja correto
        handleTypeChange();

        // Se já tiver data e profissional (vindo de um erro de validação por exemplo), carrega agenda
        if (typeof loadSchedule === 'function') loadSchedule();

    }, 100); // 100ms é o suficiente para o app.js terminar
})();
</script>
@endpush