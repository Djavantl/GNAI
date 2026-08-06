document.addEventListener('DOMContentLoaded', function () {
    const presenceToggles = document.querySelectorAll('.presence-toggle');

    presenceToggles.forEach(toggle => {
        const clearInput = (input) => {
            if (input.matches('input[type="checkbox"], input[type="radio"]')) {
                input.checked = false;
                return;
            }

            input.value = '';
        };

        // Função para atualizar o estado inicial (caso venha do old() ou edição)
        const updateState = (checkbox) => {
            const index = checkbox.id.split('_')[1];
            const evalFields = document.getElementById(`eval_fields_${index}`);
            const absenceFields = document.getElementById(`absence_fields_${index}`);
            const tabButton = document.getElementById(`tab-${index}`);
            const withGuardians = document.getElementById('with_guardians')?.checked ?? false;

            if (!evalFields || !absenceFields) {
                return;
            }

            // Seleciona todos os inputs/textareas dentro dos blocos
            const evalInputs = evalFields.querySelectorAll('input, textarea, select');
            const absenceInputs = absenceFields.querySelectorAll('input, textarea, select');

            if (checkbox.checked) {
                // Aluno Presente
                evalFields.style.display = 'block';
                absenceFields.style.display = 'none';
                
                // Habilita campos de avaliação e desabilita os de falta
                evalInputs.forEach(input => input.disabled = false);
                absenceInputs.forEach(input => {
                    input.disabled = true;
                    clearInput(input);
                });

                if (tabButton) {
                    tabButton.classList.remove('border-danger');
                    const icon = tabButton.querySelector('i');
                    if (icon) icon.className = 'fas fa-chevron-right small opacity-50';
                }
            } else if (withGuardians) {
                // Aluno ausente em atendimento realizado com responsáveis.
                // Mantém o conteúdo pedagógico e acrescenta o motivo da ausência.
                evalFields.style.display = 'block';
                absenceFields.style.display = 'block';

                evalInputs.forEach(input => input.disabled = false);
                absenceInputs.forEach(input => input.disabled = false);

                if (tabButton) {
                    tabButton.classList.add('border-danger');
                    const icon = tabButton.querySelector('i');
                    if (icon) icon.className = 'fas fa-user-times text-danger';
                }
            } else {
                // Aluno Ausente
                evalFields.style.display = 'none';
                absenceFields.style.display = 'block';

                // Desabilita campos de avaliação e habilita os de falta
                evalInputs.forEach(input => {
                    input.disabled = true;
                    clearInput(input);
                });
                absenceInputs.forEach(input => input.disabled = false);

                if (tabButton) {
                    tabButton.classList.add('border-danger');
                    const icon = tabButton.querySelector('i');
                    if (icon) icon.className = 'fas fa-user-times text-danger';
                }
            }
        };

        // Escuta a mudança
        toggle.addEventListener('change', function () {
            updateState(this);
        });

        // Executa ao carregar a página (importante para erros de validação/old)
        updateState(toggle);
    });

    const guardianToggle = document.getElementById('with_guardians');
    const guardianFields = document.getElementById('guardian_participants_fields');

    if (guardianToggle && guardianFields) {
        const updateGuardianFields = () => {
            guardianFields.classList.toggle('d-none', !guardianToggle.checked);
            guardianFields.querySelectorAll('.guardian-participant-input').forEach(input => {
                input.disabled = !guardianToggle.checked;

                if (!guardianToggle.checked) {
                    input.checked = false;
                }
            });

            presenceToggles.forEach(toggle => {
                toggle.dispatchEvent(new Event('change'));
            });
        };

        guardianToggle.addEventListener('change', updateGuardianFields);
        updateGuardianFields();
    }
});
