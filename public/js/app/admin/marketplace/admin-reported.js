class ReportedManager {
  constructor() {
    this.currentTab = 'all';
    this.reports = [];
    this.filtered = [];
    this.isLoading = false;
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.loadReports();
  }

  setupEventListeners() {
    document.querySelectorAll('.tab-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        this.currentTab = btn.dataset.status || 'all';
        this.applyFilters();
      });
    });

    document.getElementById('search-input')?.addEventListener('input', () => this.applyFilters());
    document.getElementById('category-filter')?.addEventListener('change', () => this.applyFilters());
    document.getElementById('date-filter')?.addEventListener('change', () => this.applyFilters());

    document.getElementById('reports-tbody')?.addEventListener('click', (event) => {
      const btn = event.target.closest('[data-action]');
      if (!btn) return;

      const action = btn.dataset.action;
      const reportId = parseInt(btn.dataset.reportId || '0', 10);
      if (!reportId) return;

      if (action === 'status') {
        this.handleStatusUpdate(reportId, btn.dataset.status || 'pending');
      } else if (action === 'chat') {
        window.location.href = `/dashboard/marketplace/admin/reported/${reportId}/chat`;
      } else if (action === 'seller-profile') {
        const sellerId = parseInt(btn.dataset.sellerId || '0', 10);
        if (sellerId > 0) {
          window.location.href = `/dashboard/marketplace/admin/sellers/${sellerId}`;
        }
      } else if (action === 'hide-product') {
        this.handleHideProduct(reportId);
      } else if (action === 'unhide-product') {
        this.handleUnhideProduct(reportId);
      }
    });
  }

  async loadReports() {
    if (this.isLoading) return;
    this.isLoading = true;

    try {
      const response = await fetch('/dashboard/marketplace/admin/reported/data');
      const data = await response.json();
      if (!response.ok || !data.success) {
        throw new Error(data.message || 'Failed to load reports');
      }

      this.reports = Array.isArray(data.items) ? data.items : [];
      this.applyFilters();
    } catch (error) {
      console.error(error);
      this.reports = [];
      this.filtered = [];
      this.renderRows();
    } finally {
      this.isLoading = false;
    }
  }

  applyFilters() {
    const term = (document.getElementById('search-input')?.value || '').toLowerCase().trim();
    const categoryFilter = document.getElementById('category-filter')?.value || '';
    const dateFilter = document.getElementById('date-filter')?.value || '';
    const now = new Date();

    this.filtered = this.reports.filter((report) => {
      if (this.currentTab !== 'all' && report.status !== this.currentTab) return false;
      if (categoryFilter && report.category !== categoryFilter) return false;

      if (term) {
        const text = `${report.id} ${report.product_title} ${report.reporter_name} ${report.seller_name} ${report.reason}`.toLowerCase();
        if (!text.includes(term)) return false;
      }

      if (dateFilter) {
        const date = new Date(report.created_at);
        if (Number.isNaN(date.getTime())) return false;
        if (dateFilter === 'today' && date.toDateString() !== now.toDateString()) return false;
        if (dateFilter === 'week' && date < new Date(now.getTime() - (7 * 86400000))) return false;
        if (dateFilter === 'month' && date < new Date(now.getTime() - (30 * 86400000))) return false;
      }

      return true;
    });

    this.renderRows();
    this.updateEmptyState();
  }

  renderRows() {
    const tbody = document.getElementById('reports-tbody');
    if (!tbody) return;

    if (!this.filtered.length) {
      tbody.innerHTML = '';
      return;
    }

    tbody.innerHTML = this.filtered.map((report) => {
      const reportId = Number(report.id);
      const badgeClass = this.escapeHtml(report.status || 'pending');
      const statusText = this.getStatusText(report.status || 'pending');
      const reasonText = this.escapeHtml(report.reason || '');
      const categoryClass = this.escapeHtml(report.category || 'other');
      const categoryText = this.getCategoryText(report.category || 'other');
      const createdAt = this.formatDate(report.created_at);
      const orderLabel = report.order_id ? `Order #${Number(report.order_id)}` : 'Order: N/A';
      const sellerId = Number(report.seller_id || 0);
      const hideBtn = report.is_hidden_by_admin
        ? `<button class="action-btn show-btn" data-action="unhide-product" data-report-id="${reportId}">Unhide Item</button>`
        : `<button class="action-btn hide-btn" data-action="hide-product" data-report-id="${reportId}">Hide Item</button>`;
      const hiddenTag = report.is_hidden_by_admin
        ? `<div class="hidden-indicator">⚠ Hidden by Admin</div>`
        : '';
      const hiddenReason = report.is_hidden_by_admin && report.hidden_by_admin_reason
        ? `<div class="moderation-meta">Hidden reason: ${this.escapeHtml(report.hidden_by_admin_reason)}</div>`
        : '';

      return `
      <tr class="report-row" data-status="${this.escapeHtml(report.status || 'pending')}" data-category="${categoryClass}">
        <td class="report-id">#RPT-${String(reportId).padStart(4, '0')}</td>
        <td class="item-details">
          <div class="item-info">
            <img src="${this.escapeHtml(report.product_image || '/images/placeholders/product.png')}" alt="Item" class="item-image">
            <div class="item-text">
              <div class="item-name">${this.escapeHtml(report.product_title || 'Unknown Product')}</div>
              <div class="item-price">Rs. ${this.formatCurrency(report.product_price || 0)}</div>
              <div class="item-price">${orderLabel}</div>
              ${hiddenTag}
            </div>
          </div>
        </td>
        <td class="reporter-info">
          <div class="user-name">${this.escapeHtml(report.reporter_name || 'Unknown')}</div>
          <div class="user-email">${this.escapeHtml(report.reporter_email || '')}</div>
        </td>
        <td class="seller-info">
          <div class="user-name">${this.escapeHtml(report.seller_name || 'Unknown')}</div>
          <div class="user-email">${this.escapeHtml(report.seller_email || '')}</div>
          <div class="user-email">Warnings: ${Number(report.warning_count || 0)} ${report.is_banned ? '• BANNED' : ''}</div>
        </td>
        <td class="report-reason">
          <span class="reason-tag ${categoryClass}">${categoryText}</span>
          <div class="reason-text">${reasonText}</div>
          ${hiddenReason}
        </td>
        <td class="date-reported">${createdAt}</td>
        <td class="status">
          <span class="status-badge ${badgeClass}">${statusText}</span>
        </td>
        <td class="actions">
          <button class="action-btn review-btn" data-action="status" data-status="under-review" data-report-id="${reportId}">Under Review</button>
          <button class="action-btn review-btn" data-action="status" data-status="resolved" data-report-id="${reportId}">Resolve</button>
          <button class="action-btn archive-btn" data-action="status" data-status="archived" data-report-id="${reportId}">Archive</button>
          ${hideBtn}
          <button class="action-btn chat-btn" data-action="chat" data-report-id="${reportId}">Open Chat</button>
          <button class="action-btn view-btn" data-action="seller-profile" data-seller-id="${sellerId}">View Seller Profile</button>
        </td>
      </tr>`;
    }).join('');
  }

  async handleStatusUpdate(reportId, status) {
    const response = await this.postForm('/dashboard/marketplace/admin/reported/update-status', {
      report_id: String(reportId),
      status,
    });
    if (!response?.success) {
      alert(response?.message || 'Failed to update status');
      return;
    }
    await this.loadReports();
  }

  async handleHideProduct(reportId) {
    const reason = window.prompt('Reason for hiding this item:');
    if (!reason || !reason.trim()) {
      return;
    }

    const response = await this.postForm('/dashboard/marketplace/admin/reported/hide-product', {
      report_id: String(reportId),
      reason: reason.trim(),
    });
    if (!response?.success) {
      alert(response?.message || 'Failed to hide product');
      return;
    }
    await this.loadReports();
  }

  async handleUnhideProduct(reportId) {
    const confirmed = window.confirm('Unhide this item and show it in storefront again?');
    if (!confirmed) {
      return;
    }

    const response = await this.postForm('/dashboard/marketplace/admin/reported/unhide-product', {
      report_id: String(reportId),
    });
    if (!response?.success) {
      alert(response?.message || 'Failed to unhide product');
      return;
    }
    await this.loadReports();
  }

  updateEmptyState() {
    const empty = document.getElementById('empty-state');
    if (!empty) return;
    empty.style.display = this.filtered.length ? 'none' : 'block';
  }

  getStatusText(status) {
    const map = {
      pending: 'Pending Review',
      'under-review': 'Under Review',
      resolved: 'Resolved',
      archived: 'Archived',
    };
    return map[status] || status;
  }

  getCategoryText(category) {
    const map = {
      inappropriate: 'Inappropriate Content',
      spam: 'Spam',
      fraud: 'Fraud/Scam',
      copyright: 'Copyright Violation',
      other: 'Other',
    };
    return map[category] || category;
  }

  formatDate(value) {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '-';
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
  }

  formatCurrency(value) {
    const num = Number(value || 0);
    return Number.isFinite(num) ? num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00';
  }

  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = String(text ?? '');
    return div.innerHTML;
  }

  async postForm(url, payload) {
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(payload),
      });
      return await response.json();
    } catch (error) {
      console.error(error);
      return { success: false, message: 'Network error' };
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  window._reportedManager = new ReportedManager();
});