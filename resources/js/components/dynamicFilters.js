export default function initDynamicFilters() {
    const forms = document.querySelectorAll('[data-dynamic-filter]');

    forms.forEach(form => {
        const targetSelector = form.dataset.target;
        const container = document.querySelector(targetSelector);

        if (!container) return;

        let timeout = null;

        form.querySelectorAll('[data-filter-input]').forEach(input => {
            input.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(fetchResults, 400);
            });
        });

        function fetchResults(pageUrl = null) {
            const params = new URLSearchParams(new FormData(form));
            const url = pageUrl || `${window.location.pathname}?${params.toString()}`;

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                attachPagination();
            });
        }

        function attachPagination() {
            container.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    fetchResults(link.href);
                });
            });
        }
    });
}
