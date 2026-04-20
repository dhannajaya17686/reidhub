class AdminSellerModerationManager {
  constructor() {
    this.currentState = 'all';
    this.rows = [];
    this.filtered = [];
    this.init();
  }

  init() {
    this.setupFilters();
    this.setupActions();
    this.load();
  }

  setupFilters() {
    document.querySelectorAll('.tab-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        this.currentState = btn.dataset.state || 'all';
        this.applyFilters();
      });
    });

    document.getElementById('search-input')?.addEventListener('input', () => this.applyFilters());
  }

  setupActions() {
    document.getElementById('moderation-tbody')?.addEventListener('click', (event) => {
      const button = event.target.closest('[data-action]');
      if (!button) return;

      const action = button.dataset.action;
      if (action !== 'view') {
        return;
      }

      const sellerId = Number(button.dataset.sellerId || 0);
      if (sellerId > 0) {
        window.location.href = `/dashboard/marketplace/admin/sellers/${sellerId}`;
      }
    });
  }

  async load() {
    try {
      const response = await fetch('/dashboard/marketplace/admin/sellers/data');
      const data = await response.json();
      if (!response.ok || !data.success) {
        throw new Error(data.message || 'Failed to load seller moderation data');
      }

      this.rows = Array.isArray(data.items) ? data.items : [];
      this.applyFilters();
    } catch (error) {
      console.error(error);
      this.rows = [];
      this.filtered = [];
      this.render();
    }
  }

  applyFilters() {
    const keyword = (document.getElementById('search-input')?.value || '').trim().toLowerCase();

    this.filtered = this.rows.filter((row) => {
      if (this.currentState === 'active' && row.is_banned) return false;
      if (this.currentState === 'banned' && !row.is_banned) return false;

      if (keyword) {
        const bag = `${row.seller_name} ${row.seller_email}`.toLowerCase();
        if (!bag.includes(keyword)) return false;
      }

      return true;
    });

    this.render();
  }

  render() {
    const tbody = document.getElementById('moderation-tbody');
    if (!tbody) return;

    if (!this.filtered.length) {
      tbody.innerHTML = '';
      this.updateEmptyState();
      return;
    }

    tbody.innerHTML = this.filtered.map((row) => {
      const sellerId = Number(row.seller_id || 0);
      const accountStatus = row.is_banned ? 'BANNED' : 'ACTIVE';
      const openReports = Number(row.pending_reports || 0) + Number(row.under_review_reports || 0);

      return `
        <tr class="report-row">
          <td class="seller-info">
            <div class="user-name">${this.esc(row.seller_name || 'Unknown')}</div>
            <div class="user-email">${this.esc(row.seller_email || '')}</div>
          </td>
          <td class="report-id">${Number(row.total_reports || 0)}</td>
          <td class="report-id">${openReports}</td>
          <td class="report-id">${Number(row.warning_count || 0)}</td>
          <td class="status">
            <span class="status-badge ${row.is_banned ? 'archived' : 'resolved'}">${accountStatus}</span>
          </td>
          <td class="report-id">${this.formatDate(row.last_reported_at)}</td>
          <td class="report-id">#SLR-${String(sellerId).padStart(4, '0')}</td>
          <td class="actions">
            <button class="action-btn review-btn" data-action="view" data-seller-id="${sellerId}">View Profile</button>
          </td>
        </tr>
      `;
    }).join('');

    this.updateEmptyState();
  }

  updateEmptyState() {
    const empty = document.getElementById('empty-state');
    if (empty) {
      empty.style.display = this.filtered.length ? 'none' : 'block';
    }
  }

  formatDate(value) {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
      return '-';
    }
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
  }

  esc(value) {
    const element = document.createElement('div');
    element.textContent = String(value ?? '');
    return element.innerHTML;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  window._adminSellerModerationManager = new AdminSellerModerationManager();
});
