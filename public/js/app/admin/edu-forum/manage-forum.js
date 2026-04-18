document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-forum-admin-page]');
    if (!page) return;

    initSectionNavigation(page);
    initAutoSubmitFilter(page);
    initQuestionEditors(page);
    initConfirmForms(page);
    initSuspensionForm(page);
    initFlashAlerts(page);
    initSubmissionState(page);
});

function initSectionNavigation(page) {
    const tabs = Array.from(page.querySelectorAll('[data-admin-tab]'));
    if (!tabs.length) return;

    const sectionMap = new Map();
    tabs.forEach((tab) => {
        const targetId = tab.getAttribute('data-admin-tab');
        const section = targetId ? page.querySelector(`#${targetId}`) : null;
        if (section) {
            sectionMap.set(targetId, section);
        }

        tab.addEventListener('click', () => {
            setActiveTab(targetId);
        });
    });

    const setActiveTab = (activeId) => {
        tabs.forEach((tab) => {
            const isActive = tab.getAttribute('data-admin-tab') === activeId;
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-current', isActive ? 'true' : 'false');
        });
    };

    const updateFromHash = () => {
        const hashId = window.location.hash ? window.location.hash.slice(1) : '';
        if (sectionMap.has(hashId)) {
            setActiveTab(hashId);
        }
    };

    if ('IntersectionObserver' in window && sectionMap.size) {
        const observer = new IntersectionObserver((entries) => {
            const visibleEntries = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

            if (!visibleEntries.length) return;

            setActiveTab(visibleEntries[0].target.id);
        }, {
            rootMargin: '-18% 0px -58% 0px',
            threshold: [0.1, 0.25, 0.5]
        });

        sectionMap.forEach((section) => observer.observe(section));
    }

    window.addEventListener('hashchange', updateFromHash);
    updateFromHash();

    if (!window.location.hash && tabs[0]) {
        setActiveTab(tabs[0].getAttribute('data-admin-tab'));
    }
}

function initAutoSubmitFilter(page) {
    const form = page.querySelector('[data-filter-form]');
    if (!form) return;

    const statusSelect = form.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.addEventListener('change', () => form.submit());
    }
}

function initQuestionEditors(page) {
    const toggles = page.querySelectorAll('[data-question-editor-toggle]');
    const rows = page.querySelectorAll('[data-question-editor-row]');
    const cancelButtons = page.querySelectorAll('[data-question-editor-cancel]');
    const textareas = page.querySelectorAll('[data-editor-textarea]');
    const answerToggles = page.querySelectorAll('[data-answer-editor-toggle]');
    const answerRows = page.querySelectorAll('[data-answer-editor-row]');
    const answerCancelButtons = page.querySelectorAll('[data-answer-editor-cancel]');
    const commentToggles = page.querySelectorAll('[data-comment-editor-toggle]');
    const commentRows = page.querySelectorAll('[data-comment-editor-row]');
    const commentCancelButtons = page.querySelectorAll('[data-comment-editor-cancel]');
    const reportToggles = page.querySelectorAll('[data-report-review-toggle]');
    const reportRows = page.querySelectorAll('[data-report-review-row]');
    const reportCancelButtons = page.querySelectorAll('[data-report-review-cancel]');
    if ((!toggles.length || !rows.length) && (!answerToggles.length || !answerRows.length) && (!commentToggles.length || !commentRows.length) && (!reportToggles.length || !reportRows.length)) return;

    const closeAllEditors = () => {
        rows.forEach((row) => {
            row.hidden = true;
        });

        answerRows.forEach((row) => {
            row.hidden = true;
        });

        commentRows.forEach((row) => {
            row.hidden = true;
        });

        reportRows.forEach((row) => {
            row.hidden = true;
        });

        toggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.textContent = 'Edit';
        });

        answerToggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.textContent = 'Edit';
        });

        commentToggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.textContent = 'Edit';
        });

        reportToggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', 'false');
            toggle.textContent = 'Review';
        });
    };

    toggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
            const questionId = toggle.getAttribute('data-question-id');
            const targetRow = page.querySelector(`[data-question-editor-row][data-question-id="${questionId}"]`);
            if (!targetRow) return;

            const willOpen = targetRow.hidden;
            closeAllEditors();

            if (!willOpen) {
                return;
            }

            targetRow.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
            toggle.textContent = 'Close';
            targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    answerToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const answerId = toggle.getAttribute('data-answer-id');
            const targetRow = page.querySelector(`[data-answer-editor-row][data-answer-id="${answerId}"]`);
            if (!targetRow) return;

            const willOpen = targetRow.hidden;
            closeAllEditors();

            if (!willOpen) {
                return;
            }

            targetRow.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
            toggle.textContent = 'Close';
            targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    commentToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const commentId = toggle.getAttribute('data-comment-id');
            const targetRow = page.querySelector(`[data-comment-editor-row][data-comment-id="${commentId}"]`);
            if (!targetRow) return;

            const willOpen = targetRow.hidden;
            closeAllEditors();

            if (!willOpen) {
                return;
            }

            targetRow.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
            toggle.textContent = 'Close';
            targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    reportToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const reportId = toggle.getAttribute('data-report-id');
            const targetRow = page.querySelector(`[data-report-review-row][data-report-id="${reportId}"]`);
            if (!targetRow) return;

            const willOpen = targetRow.hidden;
            closeAllEditors();

            if (!willOpen) {
                return;
            }

            targetRow.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
            toggle.textContent = 'Close';
            targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    cancelButtons.forEach((button) => {
        button.addEventListener('click', () => {
            closeAllEditors();
        });
    });

    answerCancelButtons.forEach((button) => {
        button.addEventListener('click', () => {
            closeAllEditors();
        });
    });

    commentCancelButtons.forEach((button) => {
        button.addEventListener('click', () => {
            closeAllEditors();
        });
    });

    reportCancelButtons.forEach((button) => {
        button.addEventListener('click', () => {
            closeAllEditors();
        });
    });

    textareas.forEach((textarea) => {
        const syncHeight = () => {
            textarea.style.height = 'auto';
            textarea.style.height = `${textarea.scrollHeight}px`;
        };

        textarea.addEventListener('input', syncHeight);
        syncHeight();
    });
}

function initConfirmForms(page) {
    const forms = page.querySelectorAll('[data-confirm-form], [data-report-form]');
    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            const submitter = event.submitter;
            if (!submitter) return;

            const action = submitter.value || submitter.textContent || 'continue';
            const actionText = String(action).trim().toLowerCase();

            const isQuestionDelete = actionText === 'delete' && !!form.querySelector('input[name="question_title"]');

            if (isQuestionDelete) {
                const noteInput = form.querySelector('input[name="moderation_note"]');
                const questionTitle = form.querySelector('input[name="question_title"]')?.value?.trim();

                if (noteInput) {
                    const promptLabel = questionTitle
                        ? `Enter the reason for deleting "${questionTitle}":`
                        : 'Enter the reason for deleting this question:';
                    const reason = window.prompt(promptLabel, noteInput.value || '');

                    if (reason === null) {
                        event.preventDefault();
                        return;
                    }

                    const trimmedReason = reason.trim();
                    if (!trimmedReason) {
                        window.alert('Please enter a delete reason so the user can be informed.');
                        event.preventDefault();
                        return;
                    }

                    noteInput.value = trimmedReason;
                }
            }

            const message = 'Confirm action: ' + action + '?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
}

function initSuspensionForm(page) {
    const form = page.querySelector('[data-suspension-form]');
    if (!form) return;

    const permanentCheckbox = form.querySelector('input[name="is_permanent"]');
    const durationInput = form.querySelector('input[name="duration_days"]');
    if (!permanentCheckbox || !durationInput) return;

    const syncDurationState = () => {
        const disabled = permanentCheckbox.checked;
        durationInput.disabled = disabled;
        durationInput.setAttribute('aria-disabled', disabled ? 'true' : 'false');
    };

    permanentCheckbox.addEventListener('change', syncDurationState);
    syncDurationState();
}

function initFlashAlerts(page) {
    const alerts = page.querySelectorAll('[data-flash-alert]');
    if (!alerts.length) return;

    window.setTimeout(() => {
        alerts.forEach((alertEl) => {
            alertEl.classList.add('is-fading');
            window.setTimeout(() => alertEl.remove(), 280);
        });
    }, 3200);
}

function initSubmissionState(page) {
    page.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.addEventListener('click', () => {
            const form = button.form;
            if (!form) return;

            form.__lastSubmitter = {
                name: button.name || '',
                value: button.value || ''
            };
        });
    });

    const forms = page.querySelectorAll('form');
    forms.forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.submitting === '1') return;
            form.dataset.submitting = '1';

            const submitter = event.submitter || form.__lastSubmitter || null;
            if (submitter && submitter.name) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = submitter.name;
                hiddenInput.value = submitter.value;
                hiddenInput.setAttribute('data-generated-submit-value', '1');

                form.querySelectorAll('[data-generated-submit-value="1"]').forEach((input) => input.remove());
                form.appendChild(hiddenInput);
            }

            const buttons = form.querySelectorAll('button[type="submit"]');
            buttons.forEach((button) => {
                button.disabled = true;
            });
        });
    });
}
