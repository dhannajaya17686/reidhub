/**
 * Transactions Manager - Mock UI Implementation
 * Handles transaction display, filtering, sorting, and pagination
 */

class TransactionsManager {
  constructor() {
    this.transactions = [];
    this.filteredTransactions = [];
    this.currentPage = 1;
    this.itemsPerPage = 10;
    this.sortField = 'date';
    this.sortDirection = 'desc';
    this.currentTab = 'all';
    this.filters = { search: '', status: '', payment: '', date: '' };
    this.init();
  }

  async init() {
    await this.loadTransactions();   // wait for API
    this.setupEventListeners();
    this.setupTabs();
    this.applyFilters();             // render after data arrives
    this.updateCounts();
    this.renderTable();
  }

  async loadTransactions() {
    try {
      const r = await fetch('/dashboard/marketplace/transactions/data');
      const j = await r.json();
      if (r.ok && j?.success && Array.isArray(j.transactions)) {
        this.transactions = j.transactions.map(t => ({
          id: t.id || `TX${t.transaction_id}`,
          transaction_id: t.transaction_id,
          buyer_id: t.buyer_id,
          item_count: t.item_count,
          total_amount: Number(t.total_amount || 0),
          created_at: t.created_at,
          orders: (t.orders || []).map(o => ({
            id: o.id,
            product_title: o.product_title,
            seller_name: o.seller_name,
            quantity: o.quantity,
            unit_price: Number(o.unit_price || 0),
            payment_method: o.payment_method,
            status: o.status
          }))
        }));
      } else {
        this.transactions = [];
        console.warn('Transactions API failed');
      }
    } catch (e) {
      console.warn('Transactions API error', e);
      this.transactions = [];
    }
  }

  setupEventListeners() {
    // Search input
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        this.filters.search = e.target.value;
        this.applyFilters();
      });
    }

    // Filter selects
    ['status-filter', 'payment-filter', 'date-filter'].forEach(id => {
      const element = document.getElementById(id);
      if (element) {
        element.addEventListener('change', (e) => {
          this.filters[id.replace('-filter', '')] = e.target.value;
          this.applyFilters();
        });
      }
    });

    // Sortable headers
    document.querySelectorAll('.sortable').forEach(header => {
      header.addEventListener('click', () => {
        const field = header.dataset.sort;
        if (this.sortField === field) {
          this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
          this.sortField = field;
          this.sortDirection = 'desc';
        }
        this.applyFilters();
      });
    });

    // Pagination
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    
    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        if (this.currentPage > 1) {
          this.currentPage--;
          this.renderTable();
          this.updatePagination();
        }
      });
    }
    
    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        const totalPages = Math.ceil(this.filteredTransactions.length / this.itemsPerPage);
        if (this.currentPage < totalPages) {
          this.currentPage++;
          this.renderTable();
          this.updatePagination();
        }
      });
    }
  }

  setupTabs() {
    document.querySelectorAll('.tab-button').forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active state from all tabs
        document.querySelectorAll('.tab-button').forEach(t => {
          t.classList.remove('tab-button--active');
          t.setAttribute('aria-selected', 'false');
        });
        
        // Set active state
        tab.classList.add('tab-button--active');
        tab.setAttribute('aria-selected', 'true');
        
        this.currentTab = tab.dataset.tab;
        this.applyFilters();
      });
    });
  }

  applyFilters() {
    let filtered = [...this.transactions];

    // Tabs
    if (this.currentTab !== 'all') {
      filtered = filtered.filter(transaction => {
        const statuses = transaction.orders.map(order => order.status);
        switch (this.currentTab) {
          case 'recent':
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            return new Date(transaction.created_at) > weekAgo;
          case 'completed':
            return statuses.every(status => status === 'delivered');
          case 'pending':
            return statuses.some(status => status === 'yet_to_ship');
          case 'cancelled':
            return statuses.some(status => status === 'cancelled');
          default:
            return true;
        }
      });
    }

    // Search
    if (this.filters.search) {
      const s = this.filters.search.toLowerCase();
      filtered = filtered.filter(t =>
        t.id.toLowerCase().includes(s) ||
        t.orders.some(o =>
          (o.product_title || '').toLowerCase().includes(s) ||
          (o.seller_name || '').toLowerCase().includes(s)
        )
      );
    }

    // Status
    if (this.filters.status) {
      filtered = filtered.filter(t => t.orders.some(o => o.status === this.filters.status));
    }

    // Payment
    if (this.filters.payment) {
      filtered = filtered.filter(t => t.orders.some(o => o.payment_method === this.filters.payment));
    }

    // Date filter (fixed bug: use transaction inside callback)
    if (this.filters.date) {
      const now = new Date();
      filtered = filtered.filter(transaction => {
        const d = new Date(transaction.created_at);
        switch (this.filters.date) {
          case 'today':
            return d.toDateString() === now.toDateString();
          case 'week':
            return d >= new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
          case 'month':
            return d >= new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
          case 'year':
            return d.getFullYear() === now.getFullYear();
          default:
            return true;
        }
      });
    }

    // Sort
    filtered.sort((a, b) => {
      let aVal, bVal;
      switch (this.sortField) {
        case 'id':
          aVal = parseInt(String(a.id).replace('TX', ''), 10);
          bVal = parseInt(String(b.id).replace('TX', ''), 10);
          break;
        case 'date':
          aVal = new Date(a.created_at);
          bVal = new Date(b.created_at);
          break;
        case 'total':
          aVal = a.total_amount;
          bVal = b.total_amount;
          break;
        default:
          aVal = a[this.sortField];
          bVal = b[this.sortField];
      }
      return this.sortDirection === 'asc' ? (aVal > bVal ? 1 : -1) : (aVal < bVal ? 1 : -1);
    });

    this.filteredTransactions = filtered;
    this.currentPage = 1;
    this.updateCounts();
    this.renderTable();
    this.updatePagination();
  }

  updateCounts() {
    const counts = {
      all: this.transactions.length,
      recent: this.transactions.filter(t => {
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        return new Date(t.created_at) > weekAgo;
      }).length,
      completed: this.transactions.filter(t => 
        t.orders.every(order => order.status === 'delivered')
      ).length,
      pending: this.transactions.filter(t => 
        t.orders.some(order => order.status === 'yet_to_ship')
      ).length,
      cancelled: this.transactions.filter(t => 
        t.orders.some(order => order.status === 'cancelled')
      ).length
    };

    // Update tab counts
    Object.keys(counts).forEach(tab => {
      const countElement = document.getElementById(`count-${tab}`);
      if (countElement) {
        countElement.textContent = counts[tab];
      }
    });

    // Update main count
    const transactionsCount = document.getElementById('transactions-count');
    if (transactionsCount) {
      const filteredCount = this.filteredTransactions.length;
      const totalCount = this.transactions.length;
      transactionsCount.textContent = filteredCount === totalCount 
        ? `${totalCount} transaction${totalCount !== 1 ? 's' : ''} found`
        : `${filteredCount} of ${totalCount} transactions found`;
    }
  }

  renderTable() {
    const tbody = document.getElementById('transactions-tbody');
    const emptyState = document.getElementById('empty-state');
    if (!tbody || !emptyState) return;

    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    const pageTransactions = this.filteredTransactions.slice(startIndex, startIndex + this.itemsPerPage);

    if (pageTransactions.length === 0) {
      tbody.innerHTML = '';
      emptyState.style.display = 'flex';
      return;
    }

    emptyState.style.display = 'none';
    tbody.innerHTML = pageTransactions.map(transaction => {
      const statusCounts = this.getStatusCounts(transaction.orders);
      const paymentMethods = this.getPaymentMethods(transaction.orders);
      const itemPreview = transaction.orders.map(order => order.product_title).join(', ');
      return `
        <tr class="transaction-row" data-transaction-id="${transaction.transaction_id}">
          <td class="transaction-id">${transaction.id}</td>
          <td class="transaction-date">
            <time datetime="${transaction.created_at}">${this.formatDate(transaction.created_at)}</time>
            <span class="time-ago">${this.getTimeAgo(transaction.created_at)}</span>
          </td>
          <td class="transaction-items">
            <div class="items-summary">
              <div class="item-count">${transaction.item_count} item${transaction.item_count !== 1 ? 's' : ''}</div>
              <div class="item-preview" title="${itemPreview}">${itemPreview}</div>
            </div>
          </td>
          <td class="transaction-total">
            <div class="amount">Rs. ${Number(transaction.total_amount).toFixed(2)}</div>
          </td>
          <td class="payment-method">
            <div class="payment-badges">
              ${paymentMethods.map(method => `
                <span class="payment-badge payment-badge--${method.replace('_', '')}">
                  ${method === 'cash_on_delivery' ? 'Cash on Delivery' : 'Pre-order'}
                </span>
              `).join('')}
            </div>
          </td>
          <td class="transaction-status">
            <div class="status-summary">
              ${Object.entries(statusCounts).map(([status, count]) => `
                <span class="status-badge status-badge--${status === 'yet_to_ship' ? 'pending' : status}">
                  ${count} ${this.getStatusText(status)}
                </span>
              `).join('')}
            </div>
          </td>
          <td class="transaction-actions">
            <button class="action-btn action-btn--primary" onclick="viewTransaction('${transaction.transaction_id}')">
              View Details
            </button>
            <button class="action-btn action-btn--secondary" onclick="downloadInvoice('${transaction.transaction_id}')">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
              </svg>
              Invoice
            </button>
          </td>
        </tr>
      `;
    }).join('');
  }

  updatePagination() {
    const totalItems = this.filteredTransactions.length;
    const totalPages = Math.ceil(totalItems / this.itemsPerPage);
    const startItem = (this.currentPage - 1) * this.itemsPerPage + 1;
    const endItem = Math.min(this.currentPage * this.itemsPerPage, totalItems);

    // Update info
    const showingStart = document.getElementById('showing-start');
    const showingEnd = document.getElementById('showing-end');
    const totalTransactions = document.getElementById('total-transactions');

    if (showingStart) showingStart.textContent = startItem;
    if (showingEnd) showingEnd.textContent = endItem;
    if (totalTransactions) totalTransactions.textContent = totalItems;

    // Update buttons
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    if (prevBtn) {
      prevBtn.disabled = this.currentPage === 1;
    }
    if (nextBtn) {
      nextBtn.disabled = this.currentPage === totalPages;
    }

    // Update page numbers
    const pageNumbers = document.getElementById('page-numbers');
    if (pageNumbers) {
      pageNumbers.innerHTML = this.generatePageNumbers(totalPages);
    }
  }

  generatePageNumbers(totalPages) {
    if (totalPages <= 1) return '';

    let pages = [];
    const current = this.currentPage;

    // Always show first page
    if (current > 3) {
      pages.push(1);
      if (current > 4) pages.push('...');
    }

    // Show pages around current
    for (let i = Math.max(1, current - 2); i <= Math.min(totalPages, current + 2); i++) {
      pages.push(i);
    }

    // Always show last page
    if (current < totalPages - 2) {
      if (current < totalPages - 3) pages.push('...');
      pages.push(totalPages);
    }

    return pages.map(page => {
      if (typeof page === 'number') {
        return `<button class="page-btn ${page === current ? 'page-btn--active' : ''}" data-page="${page}">${page}</button>`;
      } else {
        return `<span class="ellipsis">...</span>`;
      }
    }).join('');
  }

  getStatusCounts(orders) {
    return orders.reduce((counts, order) => {
      counts[order.status] = (counts[order.status] || 0) + 1;
      return counts;
    }, {});
  }

  getPaymentMethods(orders) {
    const methods = new Set();
    orders.forEach(order => {
      if (order.payment_method) {
        methods.add(order.payment_method);
      }
    });
    return Array.from(methods);
  }

  formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
  }

  getTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) return `${diff} seconds ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    return `${Math.floor(diff / 86400)} days ago`;
  }

  getStatusText(status) {
    const statusTexts = {
      yet_to_ship: 'Pending',
      delivered: 'Completed',
      cancelled: 'Cancelled',
      returned: 'Returned'
    };
    return statusTexts[status] || status;
  }
}

function viewTransaction(transactionId) {
  window.location.href = `/dashboard/marketplace/transactions/view?id=${transactionId}`;
}

function downloadInvoice(transactionId) {
  window.location.href = `/dashboard/marketplace/transactions/invoice?id=${transactionId}`;
}

document.addEventListener('DOMContentLoaded', () => {
  window.transactionsManager = new TransactionsManager();
});