/**
 * Admin Reported Items - Minimal JavaScript
 */
class ReportedManager {
  constructor() {
    this.currentTab = 'all';
    this.reports = [];
    this.filtered = [];
    this.currentReport = null;
    this.currentSeller = null;
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.loadReports();
  }

  setupEventListeners() {
    // Tab filters
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.currentTab = btn.dataset.status || 'all';
        this.applyFilters();
      });
    });

    // Search and filters
    document.getElementById('search-input')?.addEventListener('input', () => this.applyFilters());
    document.getElementById('category-filter')?.addEventListener('change', () => this.applyFilters());
    document.getElementById('date-filter')?.addEventListener('change', () => this.applyFilters());

    // Modal close on overlay click and escape key
    document.addEventListener('click', (e) => {
      if (e.target.id === 'review-modal') this.closeReviewModal();
      if (e.target.id === 'contact-modal') this.closeContactModal();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeReviewModal();
        this.closeContactModal();
      }
    });

    // Enter key to send message
    document.getElementById('message-input')?.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') this.sendMessage();
    });
  }

  loadReports() {
    // Read existing sample data from DOM
    this.reports = this.readExistingRows();
    this.filtered = [...this.reports];
    this.updateEmpty();
  }

  readExistingRows() {
    const rows = Array.from(document.querySelectorAll('#reports-tbody .report-row'));
    return rows.map(r => ({
      id: r.querySelector('.report-id')?.textContent?.trim() || '',
      item: r.querySelector('.item-name')?.textContent?.trim() || '',
      reporter: r.querySelector('.reporter-info .user-name')?.textContent?.trim() || '',
      seller: r.querySelector('.seller-info .user-name')?.textContent?.trim() || '',
      sellerEmail: r.querySelector('.seller-info .user-email')?.textContent?.trim() || '',
      reason: r.querySelector('.reason-text')?.textContent?.trim() || '',
      date: r.querySelector('.date-reported')?.textContent?.trim() || '',
      status: r.getAttribute('data-status') || 'pending',
      category: r.getAttribute('data-category') || 'other',
      hidden: r.getAttribute('data-hidden') === 'true' || false
    }));
  }

  applyFilters() {
    const term = (document.getElementById('search-input')?.value || '').toLowerCase();
    const categorySel = document.getElementById('category-filter')?.value || '';
    const dateSel = document.getElementById('date-filter')?.value || '';

    this.filtered = this.reports.filter(r => {
      if (this.currentTab !== 'all' && r.status !== this.currentTab) return false;
      if (categorySel && r.category !== categorySel) return false;
      if (term) {
        const hay = `${r.id} ${r.item} ${r.reporter} ${r.seller} ${r.reason}`.toLowerCase();
        if (!hay.includes(term)) return false;
      }
      if (dateSel) {
        const d = new Date(r.date);
        const now = new Date();
        if (dateSel === 'today' && d.toDateString() !== now.toDateString()) return false;
        if (dateSel === 'week' && d < new Date(now.getTime() - 7 * 864e5)) return false;
        if (dateSel === 'month' && d < new Date(now.getTime() - 30 * 864e5)) return false;
      }
      return true;
    });

    this.applyFilterToDom();
    this.updateEmpty();
  }

  applyFilterToDom() {
    const ids = new Set(this.filtered.map(r => r.id));
    document.querySelectorAll('#reports-tbody .report-row').forEach(row => {
      const rid = row.querySelector('.report-id')?.textContent?.trim() || '';
      row.style.display = ids.size ? (ids.has(rid) ? '' : 'none') : '';
    });
  }

  updateEmpty() {
    const empty = document.getElementById('empty-state');
    if (!empty) return;
    const anyVisible = Array.from(document.querySelectorAll('#reports-tbody .report-row'))
      .some(r => r.style.display !== 'none');
    empty.style.display = anyVisible ? 'none' : 'block';
  }

  updateStatusLocal(reportId, newStatus) {
    const report = this.reports.find(r => r.id === reportId);
    if (report) report.status = newStatus;

    const row = Array.from(document.querySelectorAll('#reports-tbody .report-row'))
      .find(r => r.querySelector('.report-id')?.textContent?.includes(reportId));
    
    if (row) {
      row.setAttribute('data-status', newStatus);
      const badge = row.querySelector('.status-badge');
      if (badge) {
        badge.className = `status-badge ${newStatus}`;
        badge.textContent = this.getStatusText(newStatus);
      }

      // Update action buttons
      this.updateActionButtons(row, report);
    }

    this.applyFilters();
  }

  updateActionButtons(row, report) {
    const actions = row.querySelector('.actions');
    if (!actions || !report) return;

    const seller = report.seller || 'Seller';
    const sellerEmail = report.sellerEmail || '';
    const reportId = report.id;
    const isHidden = report.hidden;
    const status = report.status;

    let buttonsHtml = '';

    // Review or View button
    if (status === 'pending' || status === 'under-review') {
      buttonsHtml += `<button class="action-btn review-btn" onclick="reviewReport('${reportId}', '${status}')">Review</button>`;
    } else {
      buttonsHtml += `<button class="action-btn view-btn" onclick="viewReport('${reportId}')">View</button>`;
    }

    // Contact Seller button
    buttonsHtml += `<button class="action-btn chat-btn" onclick="contactSeller('${seller}', '${sellerEmail}')">Contact Seller</button>`;

    // Hide/Show Item button
    if (isHidden) {
      buttonsHtml += `<button class="action-btn show-btn" onclick="showItem('${reportId}')">Show Item</button>`;
    } else {
      buttonsHtml += `<button class="action-btn hide-btn" onclick="hideItem('${reportId}')">Hide Item</button>`;
    }

    // Archive/Unarchive button
    if (status === 'archived') {
      buttonsHtml += `<button class="action-btn unarchive-btn" onclick="unarchiveReport('${reportId}')">Unarchive</button>`;
    } else {
      buttonsHtml += `<button class="action-btn archive-btn" onclick="archiveReport('${reportId}')">Archive</button>`;
    }

    actions.innerHTML = buttonsHtml;
  }

  getStatusText(status) {
    const statusMap = {
      'pending': 'Pending Review',
      'under-review': 'Under Review',
      'resolved': 'Resolved',
      'archived': 'Archived'
    };
    return statusMap[status] || status;
  }

  // Item visibility methods
  hideItem(reportId) {
    if (confirm('Hide this item from the marketplace? Users won\'t be able to see or purchase it.')) {
      const report = this.reports.find(r => r.id === reportId);
      if (report) {
        report.hidden = true;
        
        const row = Array.from(document.querySelectorAll('#reports-tbody .report-row'))
          .find(r => r.querySelector('.report-id')?.textContent?.includes(reportId));
        
        if (row) {
          row.setAttribute('data-hidden', 'true');
          
          // Add visual indicator to item
          const itemInfo = row.querySelector('.item-info');
          if (itemInfo && !itemInfo.querySelector('.hidden-indicator')) {
            const indicator = document.createElement('span');
            indicator.className = 'hidden-indicator';
            indicator.innerHTML = '👁️‍🗨️ Hidden';
            indicator.title = 'This item is hidden from users';
            itemInfo.appendChild(indicator);
          }
          
          this.updateActionButtons(row, report);
        }
        
        console.log(`Item hidden: ${reportId}`);
        // Here you would make an API call to actually hide the item
        // this.apiCall('POST', `/admin/marketplace/items/hide`, { reportId, itemId: report.itemId });
      }
    }
  }

  showItem(reportId) {
    if (confirm('Show this item in the marketplace? Users will be able to see and purchase it again.')) {
      const report = this.reports.find(r => r.id === reportId);
      if (report) {
        report.hidden = false;
        
        const row = Array.from(document.querySelectorAll('#reports-tbody .report-row'))
          .find(r => r.querySelector('.report-id')?.textContent?.includes(reportId));
        
        if (row) {
          row.setAttribute('data-hidden', 'false');
          
          // Remove visual indicator
          const indicator = row.querySelector('.hidden-indicator');
          if (indicator) {
            indicator.remove();
          }
          
          this.updateActionButtons(row, report);
        }
        
        console.log(`Item shown: ${reportId}`);
        // Here you would make an API call to show the item
        // this.apiCall('POST', `/admin/marketplace/items/show`, { reportId, itemId: report.itemId });
      }
    }
  }

  // Review Modal Methods
  reviewReport(reportId, status) {
    const report = this.reports.find(r => r.id === reportId);
    if (!report) return;

    this.currentReport = report;
    
    const modal = document.getElementById('review-modal');
    const title = document.getElementById('review-title');
    const details = document.getElementById('report-details');

    if (title) title.textContent = `Review Report ${reportId}`;
    if (details) {
      details.innerHTML = `
        <div class="detail-row"><span class="detail-label">Report ID:</span><span class="detail-value">${report.id}</span></div>
        <div class="detail-row"><span class="detail-label">Item:</span><span class="detail-value">${report.item}</span></div>
        <div class="detail-row"><span class="detail-label">Reporter:</span><span class="detail-value">${report.reporter}</span></div>
        <div class="detail-row"><span class="detail-label">Seller:</span><span class="detail-value">${report.seller}</span></div>
        <div class="detail-row"><span class="detail-label">Reason:</span><span class="detail-value">${report.reason}</span></div>
        <div class="detail-row"><span class="detail-label">Date:</span><span class="detail-value">${report.date}</span></div>
        <div class="detail-row"><span class="detail-label">Status:</span><span class="detail-value">${this.getStatusText(report.status)}</span></div>
        <div class="detail-row"><span class="detail-label">Item Visibility:</span><span class="detail-value">${report.hidden ? 'Hidden from users' : 'Visible to users'}</span></div>
      `;
    }

    if (modal) {
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
  }

  closeReviewModal() {
    const modal = document.getElementById('review-modal');
    if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
    this.currentReport = null;
  }

  resolveReport() {
    if (!this.currentReport) return;
    if (confirm('Mark this report as resolved?')) {
      this.updateStatusLocal(this.currentReport.id, 'resolved');
      this.closeReviewModal();
    }
  }

  markUnderReview() {
    if (!this.currentReport) return;
    this.updateStatusLocal(this.currentReport.id, 'under-review');
    this.closeReviewModal();
  }

  dismissReport() {
    if (!this.currentReport) return;
    if (confirm('Dismiss this report? This action cannot be undone.')) {
      // Remove from reports array and DOM
      this.reports = this.reports.filter(r => r.id !== this.currentReport.id);
      const row = Array.from(document.querySelectorAll('#reports-tbody .report-row'))
        .find(r => r.querySelector('.report-id')?.textContent?.includes(this.currentReport.id));
      if (row) row.remove();
      
      this.applyFilters();
      this.closeReviewModal();
    }
  }

  // Archive/Unarchive Methods
  archiveReport(reportId) {
    if (confirm('Archive this report?')) {
      this.updateStatusLocal(reportId, 'archived');
    }
  }

  unarchiveReport(reportId) {
    if (confirm('Unarchive this report?')) {
      this.updateStatusLocal(reportId, 'pending');
    }
  }

  // Contact Modal Methods
  contactSeller(sellerName, sellerEmail) {
    this.currentSeller = { name: sellerName, email: sellerEmail };
    
    const modal = document.getElementById('contact-modal');
    const title = document.getElementById('contact-title');

    if (title) title.textContent = `Contact ${sellerName}`;
    if (modal) {
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }
  }

  closeContactModal() {
    const modal = document.getElementById('contact-modal');
    if (modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
    this.currentSeller = null;
  }

  sendMessage() {
    const input = document.getElementById('message-input');
    const messages = document.getElementById('chat-messages');
    
    if (!input || !messages || !input.value.trim()) return;

    const message = input.value.trim();
    const messageHtml = `
      <div class="message sent">
        <div class="message-content">
          <strong>Admin:</strong> ${this.escapeHtml(message)}
        </div>
        <div class="message-time">${new Date().toLocaleTimeString()}</div>
      </div>
    `;

    messages.insertAdjacentHTML('beforeend', messageHtml);
    messages.scrollTop = messages.scrollHeight;
    input.value = '';

    // Simulate seller response (for demo)
    setTimeout(() => {
      const responseHtml = `
        <div class="message received">
          <div class="message-content">
            <strong>${this.currentSeller?.name || 'Seller'}:</strong> Thank you for contacting me. I'll look into this matter.
          </div>
          <div class="message-time">${new Date().toLocaleTimeString()}</div>
        </div>
      `;
      messages.insertAdjacentHTML('beforeend', responseHtml);
      messages.scrollTop = messages.scrollHeight;
    }, 1000);
  }

  viewReport(reportId) {
    // Navigate to detailed report view (placeholder)
    console.log('Viewing report:', reportId);
    // window.location.href = `/admin/marketplace/reports/${reportId.replace('#', '')}`;
  }

  escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
  }

  // API call helper (placeholder)
  async apiCall(method, url, data = null) {
    try {
      const options = {
        method,
        headers: {
          'Content-Type': 'application/json',
        }
      };
      
      if (data) {
        options.body = JSON.stringify(data);
      }
      
      const response = await fetch(url, options);
      return await response.json();
    } catch (error) {
      console.error('API call failed:', error);
      return null;
    }
  }
}

// Global functions for inline handlers
function reviewReport(reportId, status) {
  window._reportedManager?.reviewReport(reportId, status);
}

function archiveReport(reportId) {
  window._reportedManager?.archiveReport(reportId);
}

function unarchiveReport(reportId) {
  window._reportedManager?.unarchiveReport(reportId);
}

function contactSeller(name, email) {
  window._reportedManager?.contactSeller(name, email);
}

function viewReport(reportId) {
  window._reportedManager?.viewReport(reportId);
}

function hideItem(reportId) {
  window._reportedManager?.hideItem(reportId);
}

function showItem(reportId) {
  window._reportedManager?.showItem(reportId);
}

function closeReviewModal() {
  window._reportedManager?.closeReviewModal();
}

function closeContactModal() {
  window._reportedManager?.closeContactModal();
}

function resolveReport() {
  window._reportedManager?.resolveReport();
}

function markUnderReview() {
  window._reportedManager?.markUnderReview();
}

function dismissReport() {
  window._reportedManager?.dismissReport();
}

function sendMessage() {
  window._reportedManager?.sendMessage();
}

// Expose globally
window.reviewReport = reviewReport;
window.archiveReport = archiveReport;
window.unarchiveReport = unarchiveReport;
window.contactSeller = contactSeller;
window.viewReport = viewReport;
window.hideItem = hideItem;
window.showItem = showItem;
window.closeReviewModal = closeReviewModal;
window.closeContactModal = closeContactModal;
window.resolveReport = resolveReport;
window.markUnderReview = markUnderReview;
window.dismissReport = dismissReport;
window.sendMessage = sendMessage;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  window._reportedManager = new ReportedManager();
});