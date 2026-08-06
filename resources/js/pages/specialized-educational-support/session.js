// 1. Helpers de Dados
function initSelectSearch(select) {
    if (!select || typeof window.$ === 'undefined' || !window.$.fn?.select2) return;

    const $select = window.$(select);
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }

    $select.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Selecione uma opção...',
        allowClear: true,
        language: { noResults: () => 'Nenhum resultado encontrado' }
    });
}

function getStudentsList() {
    const container = document.getElementById('students-container');
    // Se o container não existir (caso da Edição), retorna vazio, pois os campos já estão no DOM
    return JSON.parse(container?.getAttribute('data-students') || '[]');
}

// 2. Lógica de Adicionar/Remover Alunos (Usada no Create)
function createStudentRow(selectedId = '') {
    const list = getStudentsList();
    const div = document.createElement('div');
    div.className = 'd-flex align-items-center gap-2 mb-2 student-row animate__animated animate__fadeIn';
    
    // Deixamos a primeira opção vazia para o Select2 assumir o placeholder
    let options = '<option value=""></option>'; 
    list.forEach(s => {
        options += `<option value="${s.id}" ${s.id == selectedId ? 'selected' : ''}>${s.name}</option>`;
    });

    div.innerHTML = `
        <div class="flex-grow-1">
            <select name="student_ids[]" class="form-select custom-input student-select-item select-search" required>
                ${options}
            </select>
        </div>
        <button type="button" class="btn btn-outline-danger remove-student-btn">
            <i class="fas fa-trash"></i>
        </button>
    `;

    // --- MÁGICA DO SELECT2 AQUI ---
    const select = div.querySelector('select');
    
    // O Select2 precisa que o elemento esteja "quase" no DOM ou usa um timeout
    setTimeout(() => {
        initSelectSearch(select);
    }, 0);
    // ------------------------------

    div.querySelector('.remove-student-btn').onclick = function() {
        if (document.querySelectorAll('.student-row').length > 1) {
            if (typeof window.$ !== 'undefined' && window.$(select).hasClass('select2-hidden-accessible')) {
                window.$(select).select2('destroy');
            }
            div.remove();
            updateRemoveButtonsVisibility();
            loadSchedule();
        }
    };

    if (typeof window.$ !== 'undefined') {
        window.$(select).on('change', loadSchedule);
    } else {
        select.addEventListener('change', loadSchedule);
    }

    setTimeout(updateRemoveButtonsVisibility, 0);
    return div;
}

function updateRemoveButtonsVisibility() {
    const rows = document.querySelectorAll('.student-row');
    rows.forEach(row => {
        const btn = row.querySelector('.remove-student-btn');
        if (btn) btn.style.display = rows.length > 1 ? 'inline-flex' : 'none';
    });
}

// 3. Carregar Cards de Disponibilidade (CORRIGIDO PARA EDIT/CREATE)
async function loadSchedule() {
    const dateEl = document.getElementById('session_date');
    // Na edição é hidden (input), no cadastro é select. O querySelector abaixo pega ambos.
    const profEl = document.querySelector('[name="professional_id"]');
    const container = document.getElementById('schedule');
    
    const studentSelects = document.querySelectorAll('.student-select-item');
    const studentIds = Array.from(studentSelects).map(s => s.value).filter(id => id !== "");

    if (!dateEl?.value || !profEl?.value || studentIds.length === 0) {
        if (container) container.innerHTML = '<p class="text-muted text-center py-3">Selecione aluno(s), profissional e data para ver a disponibilidade.</p>';
        return;
    }

    try {
        container.innerHTML = '<div class="p-3 text-muted text-center w-100"><i class="fas fa-spinner fa-spin"></i> Verificando disponibilidade...</div>';

        const params = new URLSearchParams();
        params.append('date', dateEl.value);
        params.append('professional', profEl.value);
        studentIds.forEach(id => params.append('student_ids[]', id));

        const response = await fetch(`${window.routes.sessionAvailability}?${params.toString()}`);
        const data = await response.json();

        container.innerHTML = '';
        container.style.display = 'grid';
        container.style.gridTemplateColumns = 'repeat(7, 1fr)'; 
        container.style.gap = '10px';

        data.slots.forEach(slot => {
            const el = document.createElement('div');
            el.className = 'slot-card';
            el.dataset.busy = slot.busy;
            
            Object.assign(el.style, {
                padding: '12px 4px', borderRadius: '8px', textAlign: 'center', fontWeight: 'bold',
                border: '2px solid transparent', transition: 'all 0.2s ease', display: 'flex',
                flexDirection: 'column', justifyContent: 'center', alignItems: 'center',
                fontSize: '0.85rem', minHeight: '60px'
            });

            if (slot.busy) {
                el.style.cursor = 'not-allowed';
                let bgColor = '#e2e3e5', txtColor = '#383d41', borderColor = '#d6d8db';

                if (slot.busy_type.includes('Profissional')) {
                    bgColor = '#fff3cd'; txtColor = '#856404'; borderColor = '#ffeeba';
                }
                const hasStudents = slot.busy_type.split(',').some(name => !name.trim().includes('Profissional'));
                if (hasStudents) {
                    bgColor = '#f8d7da'; txtColor = '#842029'; borderColor = '#f5c2c7';
                }

                el.style.backgroundColor = bgColor;
                el.style.color = txtColor;
                el.style.borderColor = borderColor;
                el.innerHTML = `<span>${slot.time}</span><small style="font-size: 0.55rem; line-height: 1.1; margin-top: 4px; overflow: hidden;" title="${slot.busy_type}">${slot.busy_type}</small>`;
            } else {
                el.style.backgroundColor = '#f0fff4'; el.style.color = '#166534'; el.style.borderColor = '#bcf0da'; el.style.cursor = 'pointer';
                el.innerHTML = `<span>${slot.time}</span><small style="font-size: 0.6rem; opacity: 0.7">Livre</small>`;

                el.onclick = () => {
                    const startInput = document.querySelector('select[name="start_time"]');
                    if (startInput) {
                        startInput.value = slot.time;
                        startInput.dispatchEvent(new Event('change'));
                    }
                    document.querySelectorAll('.slot-card').forEach(c => {
                        c.classList.remove('selected');
                        if (c.dataset.busy === "false") {
                            c.style.backgroundColor = '#f0fff4'; c.style.color = '#166534'; c.style.borderColor = '#bcf0da';
                        }
                    });
                    el.classList.add('selected');
                    el.style.backgroundColor = '#166534'; el.style.color = '#ffffff'; el.style.borderColor = '#064e3b';
                };
            }
            container.appendChild(el);
        });
    } catch (error) {
        container.innerHTML = '<div class="text-danger p-3 text-center">Erro ao carregar horários.</div>';
    }
}

// 4. Lógica de Bloqueio Manhã/Tarde
function updateEndTimeOptions() {
    const startSelect = document.querySelector('select[name="start_time"]');
    const endTimeSelect = document.querySelector('select[name="end_time"]');
    if (!startSelect?.value || !endTimeSelect) return;

    const startTime = startSelect.value;
    const startHour = parseInt(startTime.split(':')[0]);
    const isMorning = startHour < 12;

    Array.from(endTimeSelect.options).forEach(option => {
        if (!option.value) return;
        const optionHour = parseInt(option.value.split(':')[0]);
        const optionMinutes = parseInt(option.value.split(':')[1]);

        if (isMorning) {
            const isAfterNoon = optionHour > 12 || (optionHour === 12 && optionMinutes > 0);
            option.hidden = isAfterNoon || option.value <= startTime;
        } else {
            const isBeforeAfternoon = optionHour < 14;
            option.hidden = isBeforeAfternoon || option.value <= startTime;
        }
    });

    if (endTimeSelect.selectedOptions[0]?.hidden) {
        const firstVisible = Array.from(endTimeSelect.options).find(opt => !opt.hidden && opt.value);
        if (firstVisible) endTimeSelect.value = firstVisible.value;
    }
}

function bindEmailConfirmationModal(form) {
    const modalSelector = form.dataset.emailConfirmationModal;
    const modal = modalSelector ? document.querySelector(modalSelector) : null;

    if (!modal || typeof window.bootstrap === 'undefined') return;

    let emailChoiceConfirmed = false;

    modal.querySelectorAll('[data-email-confirmation-choice]').forEach(button => {
        button.addEventListener('click', () => {
            emailChoiceConfirmed = true;
        });
    });

    form.addEventListener('submit', event => {
        if (emailChoiceConfirmed) return;

        const dateInput = form.querySelector('[name="session_date"]');
        const sendButton = modal.querySelector('[data-send-notification-button]');

        if (dateInput && sendButton) {
            const today = new Date();
            const localToday = [
                today.getFullYear(),
                String(today.getMonth() + 1).padStart(2, '0'),
                String(today.getDate()).padStart(2, '0'),
            ].join('-');
            const isPastDate = dateInput.value < localToday;

            if (isPastDate) return;
        }

        event.preventDefault();
        window.bootstrap.Modal.getOrCreateInstance(modal).show();
    });
}

// 5. Inicialização INTELIGENTE (Create vs Edit)
document.addEventListener('DOMContentLoaded', function () {
    const attendanceTypeSelect = document.getElementById('attendance_type');
    const typeSelect = document.getElementById('session_type');
    const addBtn = document.getElementById('add-student-btn');
    const container = document.getElementById('students-container');
    const dateInput = document.getElementById('session_date');
    const startTimeInput = document.querySelector('select[name="start_time"]');

    document.querySelectorAll('form[data-email-confirmation-modal]').forEach(bindEmailConfirmationModal);

    // Se o container de alunos existir (TELA DE CADASTRO)
    if (container) {
        function syncUI() {
            const isPedagogical = attendanceTypeSelect?.value === 'pedagogical';
            const form = typeSelect?.closest('form');
            let lockedTypeInput = form?.querySelector('[data-locked-session-type]');

            if (isPedagogical && typeSelect) {
                typeSelect.value = 'individual';
                typeSelect.disabled = true;

                if (!lockedTypeInput) {
                    lockedTypeInput = document.createElement('input');
                    lockedTypeInput.type = 'hidden';
                    lockedTypeInput.name = 'type';
                    lockedTypeInput.dataset.lockedSessionType = 'true';
                    form?.appendChild(lockedTypeInput);
                }

                lockedTypeInput.value = 'individual';
            } else if (typeSelect) {
                typeSelect.disabled = false;
                lockedTypeInput?.remove();
            }

            if (!container.querySelector('.student-row')) {
                container.appendChild(createStudentRow());
            }

            if (isPedagogical) {
                Array.from(container.querySelectorAll('.student-row'))
                    .slice(1)
                    .forEach(row => {
                        const select = row.querySelector('select');
                        if (select && typeof window.$ !== 'undefined' && window.$(select).hasClass('select2-hidden-accessible')) {
                            window.$(select).select2('destroy');
                        }
                        row.remove();
                    });
            }

            if (!addBtn) return;

            if (!isPedagogical && typeSelect?.value === 'group') addBtn.classList.remove('d-none');
            else addBtn.classList.add('d-none');

            updateRemoveButtonsVisibility();
            loadSchedule();
        }

        attendanceTypeSelect?.addEventListener('change', syncUI);
        typeSelect?.addEventListener('change', syncUI);
        if (addBtn) {
            addBtn.onclick = () => {
                container.appendChild(createStudentRow());
                updateRemoveButtonsVisibility();
            };
        }
        syncUI();
    } else {
        const syncEditType = () => {
            if (!attendanceTypeSelect || !typeSelect) return;

            if (attendanceTypeSelect.value === 'pedagogical') {
                typeSelect.value = 'individual';
            } else {
                typeSelect.disabled = false;
            }
        };

        attendanceTypeSelect?.addEventListener('change', syncEditType);
        syncEditType();
    }

    // Eventos comuns a ambas as telas
    if (dateInput) dateInput.onchange = loadSchedule;
    if (startTimeInput) startTimeInput.onchange = updateEndTimeOptions;
    
    // Profissional pode ser Select (Create) ou Hidden (Edit)
    const profEl = document.querySelector('[name="professional_id"]');
    if (profEl) profEl.onchange = loadSchedule;

    // Execução inicial para a tela de EDIÇÃO
    if (dateInput?.value) {
        loadSchedule();
        updateEndTimeOptions();
    }
    
});
