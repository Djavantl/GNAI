function initDynamicFilters() {
    const forms = document.querySelectorAll('[data-dynamic-filter]');

    forms.forEach(form => {
        const targetSelector = form.dataset.target;
        const container = document.querySelector(targetSelector);
        if (!container) return;

        let timeout = null;

        form.addEventListener('input', (e) => {
            const isFilterInput = e.target.hasAttribute('data-filter-input') ||
                e.target.tagName === 'SELECT' ||
                e.target.tagName === 'INPUT';

            if (isFilterInput) {
                clearTimeout(timeout);
                timeout = setTimeout(() => fetchResults(), 500);
            }
        });

        function fetchResults(pageUrl = null) {
            // 1. Estabiliza a altura para evitar que o conteúdo abaixo "suba" bruscamente
            container.style.minHeight = container.offsetHeight + 'px';

            // 2. Feedback visual suave (opacidade em vez de movimento)
            container.style.transition = 'opacity 0.2s ease-in-out';
            container.style.opacity = '0.4';

            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value !== '') params.append(key, value);
            }

            const baseUrl = window.location.pathname;
            const finalUrl = pageUrl || `${baseUrl}?${params.toString()}`;

            window.history.pushState({}, '', finalUrl);

            fetch(finalUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Erro na requisição');
                    return res.text();
                })
                .then(html => {
                    // 3. Substitui o conteúdo
                    container.innerHTML = html;

                    // 4. Remove a classe de animação de entrada temporariamente
                    // para evitar o "pulo" do translateY (opcional, dependendo do seu HTML)
                    const animatedElements = container.querySelectorAll('.page-transition');
                    animatedElements.forEach(el => el.classList.remove('page-transition'));

                    // 5. Restaura o container
                    container.style.opacity = '1';

                    // Delay curto para o navegador renderizar antes de liberar a altura
                    setTimeout(() => {
                        container.style.minHeight = '';
                    }, 100);

                    attachPagination();
                })
                .catch(error => {
                    console.error('Erro ao filtrar:', error);
                    container.style.opacity = '1';
                    container.style.minHeight = '';
                });
        }

        function attachPagination() {
            container.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    // Rola suavemente para o topo do container ao mudar de página
                    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    fetchResults(link.href);
                });
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', initDynamicFilters);
