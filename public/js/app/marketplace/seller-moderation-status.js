(function () {
  class SellerModerationStatus {
    constructor() {
      this.host = document.getElementById('seller-moderation-status');
      if (!this.host) return;
      this.load();
    }

    async load() {
      try {
        const response = await fetch('/dashboard/marketplace/seller/moderation/summary');
        const payload = await response.json();
        if (!response.ok || !payload.success) {
          return;
        }

        this.render(payload.summary || {
          account_status: 'active',
          warning_count: 0,
          is_banned: false,
        });
      } catch (error) {
        console.error(error);
      }
    }

    render(summary) {
      const accountStatus = summary.account_status || 'active';
      const warningCount = Number(summary.warning_count || 0);
      const isBanned = Boolean(summary.is_banned);

      const label = accountStatus === 'banned'
        ? 'BANNED'
        : (accountStatus === 'warned' ? 'WARNED' : 'ACTIVE');

      this.host.innerHTML = `
        <div class="seller-status-card seller-status-card--${this.esc(accountStatus)}">
          <div class="seller-status-card__head">
            <span class="seller-status-card__title">Seller Account Status</span>
            <span class="seller-status-card__badge">${label}</span>
          </div>
          <div class="seller-status-card__meta">Warnings: ${warningCount}</div>
          ${warningCount > 0 ? '<div class="seller-status-card__warning">⚠ You have active moderation warnings. Please follow marketplace policies to avoid stronger actions.</div>' : ''}
          ${isBanned ? '<div class="seller-status-card__ban">Your account is currently banned. Adding new items is blocked until moderation lifts this action.</div>' : ''}
        </div>
      `;

      if (isBanned) {
        this.applyBanOverlay();
      }
    }

    applyBanOverlay() {
      const context = this.host.dataset.moderationContext || '';
      if (context !== 'add-items') {
        return;
      }

      const form = document.getElementById('add-item-form');
      if (!form) {
        return;
      }

      form.classList.add('ban-disabled');
      const controls = form.querySelectorAll('input, select, textarea, button');
      controls.forEach((control) => {
        if (!(control instanceof HTMLElement)) return;
        control.setAttribute('disabled', 'disabled');
      });

      const overlay = document.createElement('div');
      overlay.className = 'seller-ban-overlay';
      overlay.innerHTML = `
        <div class="seller-ban-overlay__content">
          <h2>Account Banned</h2>
          <p>You cannot add new items while this ban is active.</p>
        </div>
      `;

      const parent = form.parentElement;
      if (parent) {
        parent.style.position = 'relative';
        parent.appendChild(overlay);
      }
    }

    esc(value) {
      const node = document.createElement('div');
      node.textContent = String(value ?? '');
      return node.innerHTML;
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    window._sellerModerationStatus = new SellerModerationStatus();
  });
})();
