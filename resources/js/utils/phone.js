document.addEventListener('DOMContentLoaded', () => {
    const handlePhoneMask = (e) => {
        let value = e.target.value;
        
        // Remove tudo que não é número
        value = value.replace(/\D/g, "");

        // (77) 98120-8577 -> 11 dígitos
        // (77) 3451-1234  -> 10 dígitos
        if (value.length > 10) {
            // Formato para Celular: (00) 00000-0000
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, "($1) $2-$3");
        } else if (value.length > 5) {
            // Formato para Fixo: (00) 0000-0000
            value = value.replace(/^(\d{2})(\d{4})(\d{4}).*/, "($1) $2-$3");
        } else if (value.length > 2) {
            // Formato inicial: (00) 0000...
            value = value.replace(/^(\d{2})(\d)/, "($1) $2");
        } else if (value.length > 0) {
            // Apenas o DDD: (00
            value = value.replace(/^(\d)/, "($1");
        }

        e.target.value = value;
    };

    // Aplica em todos os inputs com a classe .phone-mask
    document.querySelectorAll('.phone-mask').forEach(input => {
        input.addEventListener('input', handlePhoneMask);
    });
});