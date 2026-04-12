document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-edu-archive-admin]');
    if (!page) return;

    initFilterForm(page);
    initTagPanel(page);
    initTagForms(page);
    initResourceForms(page);
    initFlashAlerts(page);
});

function initFilterForm(page) {
    const form = page.querySelector('[data-filter-form]');
    if (!form) return;

    const selects = form.querySelectorAll('select');
    selects.forEach((select) => {
        select.addEventListener('change', () => {
            form.submit();
        });
    });
}

function initTagPanel(page) {
    const toggle = page.querySelector('[data-tag-panel-toggle]');
    const body = page.querySelector('[data-tag-panel-body]');
    if (!toggle || !body) return;

    toggle.addEventListener('click', () => {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!isOpen));
        body.hidden = isOpen;
        const label = toggle.querySelector('.tag-panel-toggle-icon');
        if (label) {
            label.textContent = isOpen ? 'Show' : 'Hide';
        }
    });
}

function initTagForms(page) {
    const createAndUpdateForms = page.querySelectorAll('.tag-create-form, .tag-edit-form');
    createAndUpdateForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const input = form.querySelector('input[name="tag_name"]');
            if (!input) return;

            const normalized = normalizeTagName(input.value);
            input.value = normalized;

            if (!normalized) {
                event.preventDefault();
                alert('Tag name is required.');
                input.focus();
                return;
            }

            markFormSubmitting(form);
        });
    });

    const deleteForms = page.querySelectorAll('[data-tag-delete-form]');
    deleteForms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm('Remove this filter tag?')) {
                event.preventDefault();
                return;
            }
            markFormSubmitting(form);
        });
    });
}

function initResourceForms(page) {
    const forms = page.querySelectorAll('[data-resource-form]');
    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const submitter = event.submitter;
            if (!submitter) return;

            const action = submitter.value || '';

            if (action === 'reject') {
                const feedback = (form.querySelector('textarea[name="admin_feedback"]')?.value || '').trim();
                if (!feedback) {
                    event.preventDefault();
                    alert('Please enter rejection feedback before rejecting.');
                    form.querySelector('textarea[name="admin_feedback"]')?.focus();
                    return;
                }
            }

            if (['approve', 'reject', 'hide', 'unhide', 'clear_removal_request'].includes(action)) {
                const confirmationMessage = getActionConfirmation(action);
                if (!window.confirm(confirmationMessage)) {
                    event.preventDefault();
                    return;
                }
            }

            markFormSubmitting(form, submitter);
        });
    });
}

function initFlashAlerts(page) {
    const alerts = page.querySelectorAll('[data-flash-alert]');
    if (!alerts.length) return;

    window.setTimeout(() => {
        alerts.forEach((alertEl) => {
            alertEl.classList.add('is-fading');
            window.setTimeout(() => alertEl.remove(), 260);
        });
    }, 3200);
}

function normalizeTagName(value) {
    return value.replace(/\s+/g, ' ').trim();
}

function getActionConfirmation(action) {
    switch (action) {
        case 'approve':
            return 'Approve this resource?';
        case 'reject':
            return 'Reject this resource with the provided feedback?';
        case 'hide':
            return 'Hide this approved resource from students?';
        case 'unhide':
            return 'Unhide this resource and make it visible to students?';
        case 'clear_removal_request':
            return 'Mark this removal request as handled?';
        default:
            return 'Continue?';
    }
}

function markFormSubmitting(form, activeButton = null) {
    if (form.dataset.submitting === '1') {
        return;
    }

    form.dataset.submitting = '1';
    form.classList.add('is-submitting');

    const controls = form.querySelectorAll('input, textarea, select, button');
    controls.forEach((control) => {
        if (control === activeButton) return;
        control.setAttribute('aria-disabled', 'true');
    });

    const submitButtons = form.querySelectorAll('button[type="submit"]');
    submitButtons.forEach((button) => {
        button.disabled = true;
    });

    if (activeButton) {
        activeButton.disabled = false;
        activeButton.dataset.originalText = activeButton.textContent;
        activeButton.textContent = 'Processing...';
    }
}
