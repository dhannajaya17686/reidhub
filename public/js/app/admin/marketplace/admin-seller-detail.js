class AdminSellerDetailManager {
  constructor() {
    const host = document.querySelector('main[data-seller-id]');
    this.sellerId = Number(host?.dataset.sellerId || 0);
    this.payload = null;
    if (!this.sellerId) return;
    this.init();
  }

  init() {
    this.setupActions();
    this.load();
  }

  setupActions() {
    document.getElementById('warn-btn')?.addEventListener('click', () => this.issueWarning());
    document.getElementById('ban-toggle-btn')?.addEventListener('click', () => this.toggleBan());
  }

  async load() {
    try {
      const response = await fetch(`/dashboard/marketplace/admin/sellers/${this.sellerId}/data`);
      const data = await response.json();
      if (!response.ok || !data.success) {
        throw new Error(data.message || 'Failed to load seller moderation details');
      }

      this.payload = data;
      this.renderSummary(data.seller);
      this.renderReports(data.reports || []);
      this.renderHistory(data.history || []);
      this.renderReportReferences(data.reports || []);
    } catch (error) {
      console.error(error);
      alert(error.message || 'Failed to load seller moderation details');
    }
  }

  renderSummary(seller) {
    if (!seller) return;

    const openReports = Number(seller.pending_reports || 0) + Number(seller.under_review_reports || 0);
    this.setText('summary-total-reports', `Reports: ${Number(seller.total_reports || 0)}`);
    this.setText('summary-open-reports', `Open: ${openReports}`);
    this.setText('summary-warning-count', `Warnings: ${Number(seller.warning_count || 0)}`);
    this.setText('summary-ban-status', `Status: ${seller.is_banned ? 'BANNED' : 'ACTIVE'}`);

    const toggle = document.getElementById('ban-toggle-btn');
    if (toggle) {
      toggle.textContent = seller.is_banned ? 'Unban Seller' : 'Ban Seller';
      toggle.classList.toggle('archive-btn', !!seller.is_banned);
      toggle.classList.toggle('review-btn', !seller.is_banned);
    }
  }

  renderReports(reports) {
    const tbody = document.getElementById('seller-related-reports-tbody');
    if (!tbody) return;

    if (!reports.length) {
      tbody.innerHTML = '<tr><td colspan="7" class="empty-description">No related reports found.</td></tr>';
      return;
    }

    tbody.innerHTML = reports.map((report) => {
      const reportId = Number(report.id || 0);
      const orderLabel = report.order_id ? `#${Number(report.order_id)}` : 'N/A';
      return `
        <tr class="report-row">
          <td class="report-id">#RPT-${String(reportId).padStart(4, '0')}</td>
          <td class="report-id">${orderLabel}</td>
          <td class="item-details">
            <div class="item-info">
              <img src="${this.esc(report.product_image || '/images/placeholders/product.png')}" alt="Item" class="item-image">
              <div class="item-text">
                <div class="item-name">${this.esc(report.product_title || 'Unknown Product')}</div>
                <div class="item-price">Rs. ${this.currency(report.product_price || 0)}</div>
              </div>
            </div>
          </td>
          <td class="reporter-info">
            <div class="user-name">${this.esc(report.reporter_name || 'Unknown')}</div>
            <div class="user-email">${this.esc(report.reporter_email || '')}</div>
          </td>
          <td class="report-reason">
            <span class="reason-tag ${this.esc(report.category || 'other')}">${this.categoryLabel(report.category || 'other')}</span>
            <div class="reason-text">${this.esc(report.reason || '')}</div>
          </td>
          <td class="status">
            <span class="status-badge ${this.esc(report.status || 'pending')}">${this.statusLabel(report.status || 'pending')}</span>
          </td>
          <td class="actions">
            <a class="action-btn chat-btn" style="text-decoration:none;" href="/dashboard/marketplace/admin/reported/${reportId}/chat">Open Chat</a>
          </td>
        </tr>
      `;
    }).join('');
  }

  renderHistory(history) {
    const tbody = document.getElementById('seller-history-tbody');
    if (!tbody) return;

    if (!history.length) {
      tbody.innerHTML = '<tr><td colspan="5" class="empty-description">No moderation actions yet.</td></tr>';
      return;
    }

    tbody.innerHTML = history.map((row) => {
      const reportCell = row.report_id
        ? `<a class="action-btn" style="text-decoration:none;" href="/dashboard/marketplace/admin/reported/${Number(row.report_id)}/chat">#RPT-${String(Number(row.report_id)).padStart(4, '0')}</a>`
        : '<span class="user-email">General action</span>';

      return `
        <tr class="report-row">
          <td class="report-id">${this.actionLabel(row.action_type || '')}</td>
          <td class="reason-text">${this.esc(row.reason || '')}</td>
          <td>${reportCell}</td>
          <td class="user-name">${this.esc(row.admin_name || 'Admin')}</td>
          <td class="user-email">${this.formatDate(row.created_at)}</td>
        </tr>
      `;
    }).join('');
  }

  renderReportReferences(reports) {
    const select = document.getElementById('report-reference');
    if (!select) return;

    const options = ['<option value="">General account action (no report link)</option>'];
    reports.forEach((report) => {
      const reportId = Number(report.id || 0);
      if (!reportId) return;
      options.push(
        `<option value="${reportId}">#RPT-${String(reportId).padStart(4, '0')} • ${this.esc(report.product_title || 'Product')}</option>`
      );
    });
    select.innerHTML = options.join('');
  }

  async issueWarning() {
    const reason = this.getReason();
    if (!reason) {
      alert('Reason is required for warning.');
      return;
    }

    const payload = { reason };
    const reportId = this.getReportReference();
    if (reportId) payload.report_id = String(reportId);

    const response = await this.postForm(`/dashboard/marketplace/admin/sellers/${this.sellerId}/warn`, payload);
    if (!response?.success) {
      alert(response?.message || 'Failed to issue warning');
      return;
    }

    this.clearReason();
    await this.load();
  }

  async toggleBan() {
    const reason = this.getReason();
    if (!reason) {
      alert('Reason is required for ban toggle.');
      return;
    }

    const payload = { reason };
    const reportId = this.getReportReference();
    if (reportId) payload.report_id = String(reportId);

    const response = await this.postForm(`/dashboard/marketplace/admin/sellers/${this.sellerId}/toggle-ban`, payload);
    if (!response?.success) {
      alert(response?.message || 'Failed to update ban status');
      return;
    }

    this.clearReason();
    await this.load();
  }

  getReason() {
    return (document.getElementById('moderation-reason')?.value || '').trim();
  }

  clearReason() {
    const reasonInput = document.getElementById('moderation-reason');
    if (reasonInput) reasonInput.value = '';
  }

  getReportReference() {
    const raw = document.getElementById('report-reference')?.value || '';
    const id = Number(raw);
    return id > 0 ? id : null;
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

  setText(id, value) {
    const node = document.getElementById(id);
    if (node) node.textContent = value;
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

  actionLabel(actionType) {
    const map = {
      warning: 'Warning Issued',
      ban: 'Seller Banned',
      unban: 'Seller Unbanned',
    };
    return map[actionType] || actionType;
  }

  formatDate(value) {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '-';
    return date.toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
  }

  currency(value) {
    const num = Number(value || 0);
    return Number.isFinite(num)
      ? num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
      : '0.00';
  }

  esc(value) {
    const element = document.createElement('div');
    element.textContent = String(value ?? '');
    return element.innerHTML;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  window._adminSellerDetailManager = new AdminSellerDetailManager();
});
