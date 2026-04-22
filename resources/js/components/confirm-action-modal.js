const METHOD_CLASSES = ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'dark', 'new'];

function normalizeMethod(method) {
    return (method || 'POST').toString().trim().toUpperCase();
}

function setSubmitVariant(button, variant) {
    if (!button) {
        return;
    }

    METHOD_CLASSES.forEach(className => button.classList.remove(className));
    button.classList.add(variant || button.dataset.confirmDefaultVariant || 'primary');
}

function updateExtraContent(modal, templateSelector) {
    const extraNode = modal.querySelector('[data-confirm-extra]');

    if (!extraNode) {
        return;
    }

    extraNode.innerHTML = '';

    if (!templateSelector) {
        extraNode.hidden = true;
        return;
    }

    const template = document.querySelector(templateSelector);

    if (!template) {
        extraNode.hidden = true;
        return;
    }

    extraNode.innerHTML = template.tagName === 'TEMPLATE'
        ? template.innerHTML
        : template.innerHTML;
    extraNode.hidden = false;
}

function bindConfirmActionModal(modal) {
    const form = modal.querySelector('[data-confirm-form]');
    const methodInput = modal.querySelector('[data-confirm-method-input]');
    const titleNode = modal.querySelector('[data-confirm-title]');
    const messageNode = modal.querySelector('[data-confirm-message]');
    const submitButton = modal.querySelector('[data-confirm-submit-button]');
    const submitTextNode = modal.querySelector('[data-confirm-submit-text]');
    const headerTitleNode = modal.querySelector('.modal-header .modal-title');

    if (!form || !methodInput || !submitButton) {
        return;
    }

    modal.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;

        if (!trigger) {
            return;
        }

        const dataset = trigger.dataset;
        const title = dataset.confirmTitle || headerTitleNode?.textContent?.trim() || '';
        const message = dataset.confirmMessage || messageNode?.textContent?.trim() || '';
        const action = dataset.confirmAction || '';
        const method = normalizeMethod(dataset.confirmMethod);
        const submitText = dataset.confirmSubmitText || submitTextNode?.textContent?.trim() || 'Confirmar';
        const variant = dataset.confirmVariant || submitButton.dataset.confirmDefaultVariant || 'primary';
        const templateSelector = dataset.confirmTemplate || '';

        if (headerTitleNode) {
            headerTitleNode.textContent = title;
        }

        if (titleNode) {
            titleNode.textContent = title;
        }

        if (messageNode) {
            messageNode.textContent = message;
        }

        form.action = action;
        methodInput.value = method;
        methodInput.disabled = method === 'POST';
        submitTextNode.textContent = submitText;
        setSubmitVariant(submitButton, variant);
        updateExtraContent(modal, templateSelector);
    });

    modal.addEventListener('hidden.bs.modal', () => {
        updateExtraContent(modal, '');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm-modal="true"]').forEach(bindConfirmActionModal);
});
