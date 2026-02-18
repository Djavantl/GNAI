document.addEventListener('DOMContentLoaded', function () {
    const presenceToggles = document.querySelectorAll('.presence-toggle');

    presenceToggles.forEach(toggle => {
        // Função para atualizar o estado inicial (caso venha do old() ou edição)
        const updateState = (checkbox) => {
            const index = checkbox.id.split('_')[1];
            const evalFields = document.getElementById(`eval_fields_${index}`);
            const absenceFields = document.getElementById(`absence_fields_${index}`);
            const tabButton = document.getElementById(`tab-${index}`);

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
                    input.value = ''; // Limpa o valor se tinha algo
                });

                if (tabButton) {
                    tabButton.classList.remove('border-danger');
                    const icon = tabButton.querySelector('i');
                    if (icon) icon.className = 'fas fa-chevron-right small opacity-50';
                }
            } else {
                // Aluno Ausente
                evalFields.style.display = 'none';
                absenceFields.style.display = 'block';

                // Desabilita campos de avaliação e habilita os de falta
                evalInputs.forEach(input => {
                    input.disabled = true;
                    input.value = ''; // Limpa para não enviar lixo
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
});