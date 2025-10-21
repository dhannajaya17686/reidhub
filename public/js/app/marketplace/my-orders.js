class OrdersManager {
  constructor() {
    this.currentTab = 'all';
    this.orders = [];
    document.addEventListener('DOMContentLoaded', () => this.init());
  }

  init() {
    this.loadOrdersData();
    this.setupTabs();
  }

  async loadOrdersData() {
    try {
      const res = await fetch('/dashboard/marketplace/orders/get');
      const data = await res.json();
      if (!res.ok || !data?.success) throw new Error(data?.message || 'Failed to load orders');

      this.orders = (data.items || []).map(o => ({
        id: String(o.id),
        title: o.title,
        price: Number(o.price),
        quantity: Number(o.quantity),
        ordered_at: o.ordered_at,
        status: o.status,           // pending | shipped | delivered | cancelled
        statusText: o.statusText,
        statusMessage: o.statusMessage || '',
        image: o.image || '/images/placeholders/product.png'
      }));

      this.updateTabCounts();
      this.filterOrdersByTab(this.currentTab);
    } catch (e) {
      console.error(e);
      this.renderEmpty(document.getElementById('tab-content-all')?.querySelector('.orders-list'));
      this.updateTabCounts();
    }
  }

  setupTabs() {
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.tab-button');
      if (!btn) return;
      this.switchTab(btn);
    });
  }

  switchTab(tabButton) {
    const tabId = tabButton.dataset.tab;
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('tab-button--active');
      btn.setAttribute('aria-selected', 'false');
      btn.setAttribute('tabindex', '-1');
    });
    tabButton.classList.add('tab-button--active');
    tabButton.setAttribute('aria-selected', 'true');
    tabButton.setAttribute('tabindex', '0');

    this.currentTab = tabId;
    this.filterOrdersByTab(tabId);
  }

  filterOrdersByTab(tabId) {
    // Hide/show panes
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('is-hidden'));
    const target = document.getElementById(`tab-content-${tabId}`);
    if (target) target.classList.remove('is-hidden');

    // Filter
    const filtered = tabId === 'all' ? this.orders : this.orders.filter(o => o.status === tabId);
    this.renderOrders(filtered, target?.querySelector('.orders-list'));
  }

  renderOrders(orders, container) {
    if (!container) return;
    if (!orders.length) {
      this.renderEmpty(container);
      return;
    }

    container.innerHTML = orders.map(order => `
      <article class="order-item" data-order-id="${order.id}">
        <div class="order-image">
          <img src="${order.image}" alt="${this.esc(order.title)}" onerror="this.src='/images/placeholders/product.png'">
        </div>
        <div class="order-details">
          <div class="order-header">
            <h3 class="order-title">${this.esc(order.title)}</h3>
            <div class="order-price">Rs. ${order.price.toLocaleString()}</div>
          </div>
          <div class="order-meta">
            <div class="order-id">Order ID: #${order.id}</div>
            <div class="order-date">Ordered on: ${this.formatDate(order.ordered_at)}</div>
            <div class="order-quantity">Quantity: ${order.quantity}</div>
          </div>
          <div class="order-status">
            <span class="status-badge status-badge--${order.status}">${order.statusText}</span>
            <div class="status-message">${this.esc(order.statusMessage)}</div>
          </div>
        </div>
        <div class="order-actions"></div>
      </article>
    `).join('');
  }

  renderEmpty(container) {
    if (!container) return;
    container.innerHTML = `
      <div class="empty-state">
        <h3>No orders found</h3>
        <p>You don't have any orders in this category yet.</p>
        <a href="/dashboard/marketplace/merch-store" class="btn btn--primary">Start Shopping</a>
      </div>
    `;
  }

  updateTabCounts() {
    const counts = {
      all: this.orders.length,
      pending: this.orders.filter(o => o.status === 'pending').length,
      shipped: this.orders.filter(o => o.status === 'shipped').length,
      delivered: this.orders.filter(o => o.status === 'delivered').length,
      cancelled: this.orders.filter(o => o.status === 'cancelled').length
    };

    Object.entries(counts).forEach(([tab, count]) => {
      const btn = document.querySelector(`[data-tab="${tab}"]`);
      const span = btn?.querySelector('.tab-count');
      if (span) span.textContent = count;
    });

    const headerCount = document.querySelector('.orders-count');
    if (headerCount) headerCount.textContent = `${counts.all} orders found`;
  }

  formatDate(s) {
    const d = new Date(s);
    if (Number.isNaN(d.getTime())) return this.esc(String(s || ''));
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
  }

  esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }
}

// Init
new OrdersManager();