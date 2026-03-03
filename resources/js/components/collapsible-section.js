document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.ctx-toggle').forEach(button => {

        button.addEventListener('click', function () {

            const targetId = button.getAttribute('data-target');
            const target = document.getElementById(targetId);

            if (!target) return;

            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            button.setAttribute('aria-expanded', (!isExpanded).toString());

            if (isExpanded) {
                target.classList.remove('ctx-expanded');
                target.classList.add('ctx-collapsed');
            } else {
                target.classList.remove('ctx-collapsed');
                target.classList.add('ctx-expanded');

                setTimeout(() => {
                    button.closest('.ctx-section-header')
                        ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 80);
            }

        });

    });

});