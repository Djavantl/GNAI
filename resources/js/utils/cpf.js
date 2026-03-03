document.addEventListener('DOMContentLoaded', () => {
    const handleCpfMask = (e) => {
        let value = e.target.value;
        
        // Remove tudo que não é dígito
        value = value.replace(/\D/g, "");

        // Aplica a máscara progressivamente
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        } else {
            // Se passar de 11 (caso você use o mesmo campo para CNPJ no futuro), corta em 11
            value = value.substring(0, 14); 
        }

        e.target.value = value;
    };

    // Aplica o evento em todos os campos com a classe .cpf-mask
    document.querySelectorAll('.cpf-mask').forEach(input => {
        input.addEventListener('input', handleCpfMask);
    });
});