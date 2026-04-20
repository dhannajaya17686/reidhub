class SellerReportsCenterManager {
  constructor() {
    this.currentStatus = 'all';
    this.rows = [];
    this.filtered = [];
    this.init();
  }

  init() {
    this.setupFilters();
    this.load();
  }

  setupFilters() {
    document.querySelectorAll('.tab-btn').forEach((button) => {
      button.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach((tab) => tab.classList.remove('active'));
        button.classList.add('active');
        this.currentStatus = button.dataset.status || 'all';
        this.applyFilters();
      });
    });

    document.getElementById('search-input')?.addEventListener('input', () => this.applyFilters());
    document.getElementById('category-filter')?.addEventListener('change', () => this.applyFilters());
  }

  async load() {
    try {
      const response = await fetch('/dashboard/marketplace/seller/reports/data');
      const data = await response.json();
      if (!response.ok || !data.success) {
        throw new Error(data.message || 'Failed to load report center data');
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
    const searchTerm = (document.getElementById('search-input')?.value || '').trim().toLowerCase();
    const category = (document.getElementById('category-filter')?.value || '').trim();

    this.filtered = this.rows.filter((row) => {
      if (this.currentStatus !== 'all' && row.status !== this.currentStatus) {
        return false;
      }
      if (category && row.category !== category) {
        return false;
      }
      if (searchTerm) {
        const bag = `${row.id} ${row.product_title} ${row.reporter_name} ${row.reason}`.toLowerCase();
        if (!bag.includes(searchTerm)) {
          return false;
        }
      }
      return true;
    });

    this.render();
  }

  render() {
    const tbody = document.getElementById('reports-tbody');
    if (!tbody) return;

    if (!this.filtered.length) {
      tbody.innerHTML = '';
      this.updateEmptyState();
      return;
    }

    tbody.innerHTML = this.filtered.map((row) => {
      const reportId = Number(row.id || 0);
      const orderLabel = row.order_id ? `Order #${Number(row.order_id)}` : 'No order link';
      const hiddenTag = row.is_hidden_by_admin
        ? `<div class="moderation-meta" style="color:#991B1B; font-weight:600;">⚠ Hidden by Administrator due to Reports</div>`
        : '';

      return `
        <tr class="order-row">
          <td class="order-id">#RPT-${String(reportId).padStart(4, '0')}</td>
          <td>
            <div class="item-name">${this.esc(row.product_title || 'Unknown Product')}</div>
            <div class="date-placed">${orderLabel}</div>
            <div class="date-placed">Rs. ${this.currency(row.product_price || 0)}</div>
            ${hiddenTag}
          </td>
          <td>
            <span class="reason-tag ${this.esc(row.category || 'other')}">${this.categoryLabel(row.category || 'other')}</span>
            <div class="moderation-meta" style="margin-top:6px;">${this.esc(row.reason || '')}</div>
          </td>
          <td>
            <div class="user-name">${this.esc(row.reporter_name || 'Unknown')}</div>
            <div class="user-email">${this.esc(row.reporter_email || '')}</div>
          </td>
          <td>
            <span class="status-badge ${this.esc(row.status || 'pending')}">${this.statusLabel(row.status || 'pending')}</span>
          </td>
          <td class="actions">
            <a href="/dashboard/marketplace/seller/reported/${reportId}/chat" class="action-btn chat-btn" style="text-decoration:none;">Chat</a>
          </td>
        </tr>
      `;
    }).join('');

    this.updateEmptyState();
  }

  updateEmptyState() {
    const emptyState = document.getElementById('empty-state');
    if (!emptyState) return;
    emptyState.style.display = this.filtered.length ? 'none' : 'block';
  }

  statusLabel(status) {
    const map = {
      pending: 'Pending Review',
      'under-review': 'Under Review',
      resolved: 'Resolved',
      archived: 'Archived',
    };
    return map[status] || status;
  }

  categoryLabel(category) {
    const map = {
      inappropriate: 'Inappropriate Content',
      spam: 'Spam',
      fraud: 'Fraud/Scam',
      copyright: 'Copyright Violation',
      other: 'Other',
    };
    return map[category] || category;
  }

  currency(value) {
    const amount = Number(value || 0);
    return Number.isFinite(amount)
      ? amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
      : '0.00';
  }

  esc(value) {
    const element = document.createElement('div');
    element.textContent = String(value ?? '');
    return element.innerHTML;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  window._sellerReportsCenterManager = new SellerReportsCenterManager();
});
