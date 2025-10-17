document.addEventListener('DOMContentLoaded', () => {
          const form = document.querySelector('.form');
          if (!form) return;
        
          const submitBtn = form.querySelector('[type="submit"]');
        
          form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearFieldErrors();
            setSubmitting(true);
        
            try {
              const res = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin', // keep PHP session cookie
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'application/json'
                }
              });
        
              // If PHP responded with a Location redirect, follow it in the page
              if (res.redirected) {
                window.location.assign(res.url);
                return;
              }
        
              // Otherwise expect JSON
              const ct = (res.headers.get('content-type') || '').toLowerCase();
              const data = ct.includes('application/json') ? await res.json() : {};
        
              if (!res.ok || data.ok === false) {
                markFieldErrors(data.errors || { general: 'Submission failed.' });
                scrollToFirstError();
                return;
              }
        
              if (data.redirect) {
                window.location.assign(data.redirect);
              } else {
                window.location.reload();
              }
            } catch (err) {
              markFieldErrors({ general: 'Network error. Please try again.' });
              scrollToFirstError();
            } finally {
              setSubmitting(false);
            }
          });
        
          function setSubmitting(disabled) {
            if (!submitBtn) return;
            submitBtn.disabled = disabled;
            if (disabled) {
              submitBtn.dataset._text = submitBtn.textContent;
              submitBtn.textContent = 'Please wait...';
            } else if (submitBtn.dataset._text) {
              submitBtn.textContent = submitBtn.dataset._text;
              delete submitBtn.dataset._text;
            }
          }
        
          function markFieldErrors(errors) {
            for (const [name, message] of Object.entries(errors)) {
              const input = document.querySelector(`[name="${name}"]`);
              if (input) input.classList.add('input-invalid');
              const holder = document.querySelector(`[data-error-for="${name}"]`);
              if (holder) holder.textContent = message;
            }
            if (errors.general) {
              let general = document.querySelector('[data-error-for="general"]');
              if (!general) {
                general = document.createElement('small');
                general.className = 'field-error';
                general.setAttribute('data-error-for', 'general');
                form.prepend(general);
              }
              general.textContent = errors.general;
            }
          }
        
          function clearFieldErrors() {
            document.querySelectorAll('.input-invalid').forEach(i => i.classList.remove('input-invalid'));
            document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
          }
        
          function scrollToFirstError() {
            const first = document.querySelector('.input-invalid, .field-error:not(:empty)');
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        });