function initDynamicFilters() {
    const forms = document.querySelectorAll('[data-dynamic-filter]');

    forms.forEach(form => {
        const targetSelector = form.dataset.target;
        const container = document.querySelector(targetSelector);
        if (!container) return;

        let timeout = null;

        // Escuta qualquer mudança no formulário (input ou select)
        form.addEventListener('input', (e) => {
            if (e.target.hasAttribute('data-filter-input') || e.target.tagName === 'SELECT' || e.target.tagName === 'INPUT') {
                clearTimeout(timeout);
                timeout = setTimeout(() => fetchResults(), 500);
            }
        });

        function fetchResults(pageUrl = null) {
            const formData = new FormData(form);
            const params = new URLSearchParams();

            // Limpa parâmetros vazios para não sujar a query
            for (const [key, value] of formData.entries()) {
                if (value !== '') params.append(key, value);
            }

            const baseUrl = window.location.pathname;
            const finalUrl = pageUrl || `${baseUrl}?${params.toString()}`;

            // Opcional: Atualiza a URL no navegador sem recarregar a página
            window.history.pushState({}, '', finalUrl);

            fetch(finalUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Erro na requisição');
                    return res.text();
                })
                .then(html => {
                    container.innerHTML = html;
                    attachPagination();
                })
                .catch(error => console.error('Erro ao filtrar:', error));
        }

        function attachPagination() {
            // Re-anexa o evento de clique aos links da paginação que o AJAX trouxe
            container.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    fetchResults(link.href);
                });
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', initDynamicFilters);
