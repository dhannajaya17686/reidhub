/**
 * Seller Portal Orders: checkout-like popups + status actions
 * - Fix: use .manage-modal (matches markup) instead of .modal-panel
 * - Overlay/Esc close + body scroll lock
 */

class OrdersManager {
  constructor() {
    this.currentTab = 'all';
    this.orders = [];
    this.filtered = [];
    this.currentOrder = null;
    this.isUpdating = false;
    document.addEventListener('DOMContentLoaded', () => this.init());
  }

  init() {
    this.hookModalBehavior();
    this.hookFilters();
    this.hookModalButtons();
    this.loadOrders();
  }

  async loadOrders() {
    try {
      const res = await fetch('/dashboard/marketplace/seller/orders/get');
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data?.success) throw new Error(data?.message || 'Failed');

      this.orders = (data.items || []).map(o => ({
        id: String(o.id),
        item: o.title || '',
        user: o.buyer_name || '',
        date: o.created_at || '',
        status: o.status || 'yet-to-ship',
        payment: o.payment || 'cod',
        slip_path: o.slip_path || null
      }));
      this.filtered = [...this.orders];
      this.renderTable();
      this.updateEmpty();
    } catch (e) {
      console.error(e);
      // Keep existing sample rows; do not overwrite if fetch fails.
    }
  }

  hookModalBehavior() {
    // Overlay click closes (click only if target IS the overlay)
    document.addEventListener('click', (e) => {
      if (!(e.target instanceof HTMLElement)) return;
      if (e.target.id === 'manage-modal') closeManageModal();
      if (e.target.id === 'chat-modal') closeChatModal();
    });
    // Esc closes
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeManageModal();
        closeChatModal();
      }
    });
  }

  hookModalButtons() {
    // Close buttons in headers (your markup uses .close-btn)
    document.querySelectorAll('#manage-modal .close-btn').forEach(b => b.addEventListener('click', closeManageModal));
    document.querySelectorAll('#chat-modal .close-btn').forEach(b => b.addEventListener('click', closeChatModal));
  }

  hookFilters() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.currentTab = btn.dataset.status || 'all';
        this.applyFilters();
      });
    });
    document.getElementById('search-input')?.addEventListener('input', () => this.applyFilters());
    document.getElementById('status-filter')?.addEventListener('change', () => this.applyFilters());
    document.getElementById('date-filter')?.addEventListener('change', () => this.applyFilters());
  }

  applyFilters() {
    const term = (document.getElementById('search-input')?.value || '').toLowerCase();
    const statusSel = document.getElementById('status-filter')?.value || '';
    const dateSel = document.getElementById('date-filter')?.value || '';

    this.filtered = (this.orders.length ? this.orders : this.readExistingRows()).filter(o => {
      if (this.currentTab !== 'all' && o.status !== this.currentTab) return false;
      if (statusSel && o.status !== statusSel) return false;
      if (term) {
        const hay = `${o.id} ${o.item} ${o.user}`.toLowerCase();
        if (!hay.includes(term)) return false;
      }
      if (dateSel) {
        const d = new Date(o.date); const now = new Date();
        if (dateSel === 'today' && d.toDateString() !== now.toDateString()) return false;
        if (dateSel === 'week' && d < new Date(now.getTime() - 7 * 864e5)) return false;
        if (dateSel === 'month' && d < new Date(now.getTime() - 30 * 864e5)) return false;
      }
      return true;
    });

    // If we had server data, re-render; else keep sample rows and just hide unmatched
    if (this.orders.length) {
      this.renderTable();
    } else {
      this.applyFilterToDom();
    }
    this.updateEmpty();
  }

  // Renders from this.filtered (used when server data is present)
  renderTable() {
    const tbody = document.getElementById('orders-tbody');
    if (!tbody) return;
    tbody.innerHTML = this.filtered.map(o => `
      <tr class="order-row" data-status="${o.status}" data-payment="${o.payment}">
        <td class="order-id">#${o.id}</td>
        <td class="item-name">${this.esc(o.item)}</td>
        <td class="user-info"><div class="user-name">${this.esc(o.user)}</div></td>
        <td class="payment-method">
          <span class="payment-badge ${o.payment === 'preorder' ? 'preorder' : 'cod'}">
            ${o.payment === 'preorder' ? 'Pre-order' : 'Cash on Delivery'}
          </span>
        </td>
        <td class="date-placed">${this.esc(o.date)}</td>
        <td class="status">
          <span class="status-badge ${o.status}">
            ${o.status === 'delivered' ? 'Delivered' :
               o.status === 'canceled' ? 'Canceled' :
               o.status === 'returned' ? 'Returned' : 'Yet to ship'}
          </span>
        </td>
        <td class="actions">
          ${o.status === 'delivered' || o.status === 'canceled' ? `
            <button class="action-btn view-btn" onclick="viewOrder('#${o.id}')">View</button>
            <button class="action-btn chat-btn" onclick="chatWithCustomer('${this.esc(o.user)}')">Chat</button>
          ` : `
            <button class="action-btn manage-btn" onclick="manageOrder('#${o.id}', '${o.status}', '${o.payment}')">Manage</button>
            <button class="action-btn chat-btn" onclick="chatWithCustomer('${this.esc(o.user)}')">Chat</button>
          `}
        </td>
      </tr>
    `).join('');
  }

  // Read sample rows present in HTML (so filters still work without server)
  readExistingRows() {
    const rows = Array.from(document.querySelectorAll('#orders-tbody .order-row'));
    return rows.map(r => ({
      id: r.querySelector('.order-id')?.textContent?.replace('#','').trim() || '',
      item: r.querySelector('.item-name')?.textContent?.trim() || '',
      user: r.querySelector('.user-name')?.textContent?.trim() || '',
      date: r.querySelector('.date-placed')?.textContent?.trim() || '',
      status: r.getAttribute('data-status') || 'yet-to-ship',
      payment: r.getAttribute('data-payment') || 'cod',
      slip_path: null
    }));
  }

  // Hide/show sample rows according to this.filtered
  applyFilterToDom() {
    const ids = new Set(this.filtered.map(o => String(o.id)));
    document.querySelectorAll('#orders-tbody .order-row').forEach(r => {
      const rid = r.querySelector('.order-id')?.textContent?.replace('#','').trim() || '';
      r.style.display = ids.size ? (ids.has(rid) ? '' : 'none') : '';
    });
  }

  updateStatusLocal(orderIdStr, newStatus) {
    const id = String(orderIdStr).replace('#','');
    const o = this.orders.find(x => x.id === id);
    if (o) o.status = newStatus;
    // Update DOM badge if row exists already
    const row = Array.from(document.querySelectorAll('#orders-tbody .order-row')).find(r => r.querySelector('.order-id')?.textContent?.includes(`#${id}`));
    if (row) {
      row.setAttribute('data-status', newStatus);
      const badge = row.querySelector('.status-badge');
      if (badge) {
        badge.className = `status-badge ${newStatus}`;
        badge.textContent = newStatus === 'delivered' ? 'Delivered' :
                            newStatus === 'canceled' ? 'Canceled' :
                            newStatus === 'returned' ? 'Returned' : 'Yet to ship';
      }
      // Replace Manage with View if finalized
      if (newStatus === 'delivered' || newStatus === 'canceled') {
        const actions = row.querySelector('.actions');
        if (actions) {
          const user = row.querySelector('.user-name')?.textContent || '';
          actions.innerHTML = `
            <button class="action-btn view-btn" onclick="viewOrder('#${id}')">View</button>
            <button class="action-btn chat-btn" onclick="chatWithCustomer('${this.esc(user)}')">Chat</button>
          `;
        }
      }
    }
    this.applyFilters();
  }

  updateEmpty() {
    const empty = document.getElementById('empty-state');
    if (!empty) return;
    const anyVisible = Array.from(document.querySelectorAll('#orders-tbody .order-row')).some(r => r.style.display !== 'none');
    empty.style.display = (this.orders.length ? this.filtered.length === 0 : !anyVisible) ? 'block' : 'none';
  }

  esc(s) { return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
}

/* ---------- Modal controls (match your markup) ---------- */

function manageOrder(orderId, status, paymentMethod) {
  // Use .manage-modal (your panel class)
  const overlay = document.getElementById('manage-modal');
  const panel = overlay?.querySelector('.manage-modal');   // FIXED: was .modal-panel
  const title = document.getElementById('manage-title');
  const orderDetails = document.getElementById('order-details');
  const slipSection = document.getElementById('payment-slip-section');

  window._ordersManager = window._ordersManager || new OrdersManager(); // ensure instance
  const mgr = window._ordersManager;

  mgr.currentOrder = { id: orderId, status, payment: paymentMethod };

  // Find order from state or from DOM sample rows
  const id = String(orderId).replace('#','');
  const fromState = (mgr.orders.find(x => x.id === id) || mgr.readExistingRows().find(x => x.id === id));
  if (!overlay || !panel || !title || !orderDetails || !fromState) return;

  title.textContent = `Manage Order ${orderId}`;
  orderDetails.innerHTML = `
    <div class="detail-row"><span class="detail-label">Order ID:</span><span class="detail-value">${orderId}</span></div>
    <div class="detail-row"><span class="detail-label">Item:</span><span class="detail-value">${mgr.esc(fromState.item)}</span></div>
    <div class="detail-row"><span class="detail-label">Customer:</span><span class="detail-value">${mgr.esc(fromState.user)}</span></div>
    <div class="detail-row"><span class="detail-label">Payment Method:</span><span class="detail-value">${paymentMethod === 'preorder' ? 'Pre-order' : 'Cash on Delivery'}</span></div>
    <div class="detail-row"><span class="detail-label">Date Placed:</span><span class="detail-value">${mgr.esc(fromState.date)}</span></div>
  `;

  if (paymentMethod === 'preorder' && fromState.slip_path && slipSection) {
    slipSection.style.display = 'block';
    const img = document.getElementById('payment-slip-image');
    if (img) img.src = fromState.slip_path;
  } else if (slipSection) {
    slipSection.style.display = 'none';
  }

  overlay.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

async function markAsDelivered() {
  const mgr = window._ordersManager;
  if (!mgr?.currentOrder || mgr.isUpdating) return;
  const id = mgr.currentOrder.id.replace('#','');
  if (!confirm('Mark this order as delivered?')) return;

  mgr.isUpdating = true;
  try {
    const fd = new FormData(); fd.append('order_id', id);
    const res = await fetch('/dashboard/marketplace/seller/orders/mark-delivered', { method: 'POST', body: fd });
    const json = await res.json().catch(() => ({}));
    if (!res.ok || !json?.success) throw new Error(json?.message || 'Failed');
    mgr.updateStatusLocal(id, 'delivered');
    closeManageModal();
  } catch (e) {
    alert('Failed to update order.');
  } finally {
    mgr.isUpdating = false;
  }
}

async function confirmCancelOrder() {
  const mgr = window._ordersManager;
  if (!mgr?.currentOrder || mgr.isUpdating) return;
  const id = mgr.currentOrder.id.replace('#','');
  const reason = (document.getElementById('cancel-reason')?.value || '').trim();
  if (!reason) { alert('Please provide a reason for cancellation'); return; }

  mgr.isUpdating = true;
  try {
    const fd = new FormData(); fd.append('order_id', id); fd.append('reason', reason);
    const res = await fetch('/dashboard/marketplace/seller/orders/cancel', { method: 'POST', body: fd });
    const json = await res.json().catch(() => ({}));
    if (!res.ok || !json?.success) throw new Error(json?.message || 'Failed');
    mgr.updateStatusLocal(id, 'canceled');
    closeManageModal();
  } catch (e) {
    alert('Failed to cancel order.');
  } finally {
    mgr.isUpdating = false;
  }
}

function showCancelForm() {
  document.querySelector('.manage-actions')?.style && (document.querySelector('.manage-actions').style.display = 'none');
  document.getElementById('cancel-form')?.style && (document.getElementById('cancel-form').style.display = 'block');
}
function hideCancelForm() {
  document.querySelector('.manage-actions')?.style && (document.querySelector('.manage-actions').style.display = 'flex');
  const cf = document.getElementById('cancel-form'); if (cf?.style) cf.style.display = 'none';
  const input = document.getElementById('cancel-reason'); if (input) input.value = '';
}
function closeManageModal() {
  const overlay = document.getElementById('manage-modal');
  if (overlay) overlay.style.display = 'none';
  document.body.style.overflow = '';
  hideCancelForm();
  const mgr = window._ordersManager; if (mgr) mgr.currentOrder = null;
}

/* Chat popup (UI only; same behavior) */
function chatWithCustomer(name) {
  const overlay = document.getElementById('chat-modal');
  const title = document.getElementById('chat-title');
  if (title) title.textContent = `Chat with ${name}`;
  if (overlay) { overlay.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
}
function closeChatModal() {
  const overlay = document.getElementById('chat-modal');
  if (overlay) overlay.style.display = 'none';
  document.body.style.overflow = '';
}

/* View order navigation */
function viewOrder(orderIdStr) {
  const id = String(orderIdStr).replace('#','');
  window.location.href = `/marketplace/seller/orders/${id}`;
}

/* Expose for inline handlers */
window.manageOrder = manageOrder;
window.markAsDelivered = markAsDelivered;
window.confirmCancelOrder = confirmCancelOrder;
window.showCancelForm = showCancelForm;
window.hideCancelForm = hideCancelForm;
window.closeManageModal = closeManageModal;
window.chatWithCustomer = chatWithCustomer;
window.closeChatModal = closeChatModal;
window.viewOrder = viewOrder;

// Boot
window._ordersManager = new OrdersManager();