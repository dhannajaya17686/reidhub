class CartManager {
  constructor() {
    this.items = new Map(); // key: product_id, value: item (+ selected payment)
    this.init();
  }

  async init() {
    await this.loadCartData();
    this.renderItems();
    this.updateSummary();
    this.setupEventListeners();
  }

  async loadCartData() {
    try {
      const res = await fetch('/dashboard/marketplace/cart/get', { method: 'GET' });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data?.success) {
        alert(data?.message || 'Failed to load cart');
        return;
      }
      (data.items || []).forEach(it => {
        // Normalize/Default to enum values: 'cash_on_delivery' | 'preorder'
        if (!it.payment_method) {
          if (it.allowsCOD) it.payment_method = 'cash_on_delivery';
          else if (it.isPreorder) it.payment_method = 'preorder';
          else it.payment_method = null;
        }
        this.items.set(String(it.product_id), it);
      });
    } catch {
      alert('Network error while loading cart');
    }
  }

  setupEventListeners() {
    // Quantity +/- buttons
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.quantity-btn');
      if (!btn) return;
      const productId = btn.dataset.itemId;
      const input = document.querySelector(`.quantity-input[data-item-id="${productId}"]`);
      if (!input) return;

      const min = parseInt(input.dataset.min || '1', 10);
      const max = parseInt(input.dataset.max || '999', 10);
      let q = parseInt(input.value || '1', 10);
      if (isNaN(q)) q = min;

      if (btn.dataset.action === 'increase') q = Math.min(q + 1, max);
      if (btn.dataset.action === 'decrease') q = Math.max(q - 1, min);

      await this.updateQuantity(productId, q);
    });

    // Quantity direct input (numeric-only text field)
    document.addEventListener('input', (e) => {
      if (!e.target.matches('.quantity-input')) return;
      // Strip non-digits live
      e.target.value = e.target.value.replace(/\D+/g, '');
    });

    document.addEventListener('change', async (e) => {
      if (!e.target.matches('.quantity-input')) return;
      const input = e.target;
      const productId = input.dataset.itemId;
      const min = parseInt(input.dataset.min || '1', 10);
      const max = parseInt(input.dataset.max || '999', 10);
      let q = parseInt(input.value || '1', 10);
      if (isNaN(q)) q = min;
      q = Math.max(min, Math.min(q, max));
      await this.updateQuantity(productId, q);
    });

    // Remove item
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('[data-action="remove"]');
      if (!btn) return;
      const productId = btn.dataset.itemId;
      if (!confirm('Remove this item from your cart?')) return;
      await this.removeItem(productId);
    });

    // Payment option selection (persist server-side)
    document.addEventListener('change', async (e) => {
      if (!e.target.matches('.payment-radio')) return;
      const productId = e.target.dataset.itemId;
      const method = e.target.value; // 'cash_on_delivery' | 'preorder'
      const item = this.items.get(String(productId));
      if (!item) return;

      const prev = item.payment_method;
      item.payment_method = method;

      const ok = await this.updatePaymentMethod(productId, method);
      if (!ok) {
        // revert selection on failure
        item.payment_method = prev;
        const prevInput = document.querySelector(`.payment-radio[name="payment_method_${productId}"][value="${prev}"]`);
        if (prevInput) prevInput.checked = true;
      }
    });
  }

  async updateQuantity(productId, quantity) {
    try {
      const fd = new FormData();
      fd.append('product_id', String(productId));
      fd.append('quantity', String(quantity));
      const res = await fetch('/dashboard/marketplace/cart/update', { method: 'POST', body: fd });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data?.success) {
        alert(data?.message || 'Failed to update quantity');
        const stored = this.items.get(String(productId));
        const input = document.querySelector(`.quantity-input[data-item-id="${productId}"]`);
        if (stored && input) input.value = stored.quantity;
        return;
      }
      const item = this.items.get(String(productId));
      if (item) item.quantity = parseInt(data.quantity || quantity, 10);
      const input = document.querySelector(`.quantity-input[data-item-id="${productId}"]`);
      if (input) input.value = item.quantity;
      this.updateSummary();
    } catch {
      alert('Network error while updating quantity');
    }
  }

  async updatePaymentMethod(productId, method) {
    try {
      const fd = new FormData();
      fd.append('product_id', String(productId));
      fd.append('payment_method', method); // 'cash_on_delivery' | 'preorder'
      const res = await fetch('/dashboard/marketplace/cart/payment-method', { method: 'POST', body: fd });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data?.success) {
        alert(data?.message || 'Failed to update payment method');
        return false;
      }
      return true;
    } catch {
      alert('Network error while updating payment method');
      return false;
    }
  }

  async removeItem(productId) {
    try {
      const fd = new FormData();
      fd.append('product_id', String(productId));
      const res = await fetch('/dashboard/marketplace/cart/remove', { method: 'POST', body: fd });
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data?.success) {
        alert(data?.message || 'Failed to remove item');
        return;
      }
      this.items.delete(String(productId));
      const el = document.querySelector(`.cart-item[data-item-id="${productId}"]`);
      if (el) el.remove();
      this.updateCounts();
      this.updateSummary();
      if (this.items.size === 0) this.renderEmptyState();
    } catch {
      alert('Network error while removing item');
    }
  }

  renderItems() {
    const container = document.getElementById('cart-items');
    if (!container) return;
    container.innerHTML = '';

    if (this.items.size === 0) {
      this.renderEmptyState();
      this.updateCounts();
      return;
    }

    const frag = document.createDocumentFragment();

    this.items.forEach((item) => {
      const qtyMax = Math.max(1, parseInt(item.stock_quantity || 999, 10));
      const condText = item.condition === 'brand_new' ? 'Brand New' : 'Used';
      const stockBadgeText = (item.stock_quantity ?? 0) <= 0 ? 'Out of Stock' : 'In Stock';
      const stockBadgeClass = (item.stock_quantity ?? 0) <= 0 ? 'stock-badge--out-of-stock' : 'stock-badge--in-stock';

      const codAllowed = !!item.allowsCOD;
      const poAllowed = !!item.isPreorder;
      const selected = item.payment_method || (codAllowed ? 'cash_on_delivery' : (poAllowed ? 'preorder' : null));

      const article = document.createElement('article');
      article.className = 'cart-item';
      article.setAttribute('data-item-id', String(item.product_id));
      article.innerHTML = `
        <div class="item-image">
          <img src="${this.escape(item.image || '/images/placeholders/product.png')}" alt="${this.escape(item.title)}" onerror="this.src='/images/placeholders/product.png'">
          <div class="stock-badge ${stockBadgeClass}">${stockBadgeText}</div>
        </div>
        <div class="item-details">
          <div class="item-header">
            <h3 class="item-title">${this.escape(item.title)}</h3>
            <div class="item-price">Rs. ${Number(item.price).toLocaleString()}</div>
          </div>

          <div class="item-meta">
            <div class="item-condition">
              Condition: <span class="condition-badge ${item.condition === 'brand_new' ? 'condition-badge--new' : 'condition-badge--used'}">${condText}</span>
            </div>
            <div class="item-seller">Sold by: <span class="seller-name">${this.escape(item.seller_label)}</span></div>
          </div>

          <!-- Payment options -->
          <div class="payment-options" aria-label="Payment Options">
            <label class="payment-option ${codAllowed ? '' : 'payment-option--disabled'}">
              <input type="radio"
                     class="payment-radio"
                     name="payment_method_${item.product_id}"
                     data-item-id="${item.product_id}"
                     value="cash_on_delivery"
                     ${codAllowed ? '' : 'disabled'}
                     ${selected === 'cash_on_delivery' ? 'checked' : ''}>
              <span class="payment-label ${codAllowed ? '' : 'payment-label--disabled'}">
                <span class="payment-icon" aria-hidden="true">💵</span>
                <span>Cash&nbsp;on&nbsp;Delivery</span>
              </span>
            </label>

            <label class="payment-option ${poAllowed ? '' : 'payment-option--disabled'}">
              <input type="radio"
                     class="payment-radio"
                     name="payment_method_${item.product_id}"
                     data-item-id="${item.product_id}"
                     value="preorder"
                     ${poAllowed ? '' : 'disabled'}
                     ${selected === 'preorder' ? 'checked' : ''}>
              <span class="payment-label ${poAllowed ? '' : 'payment-label--disabled'}">
                <span class="payment-icon" aria-hidden="true">💳</span>
                <span>Pre&#8209;order</span>
              </span>
            </label>
          </div>

          <div class="item-actions">
            <div class="quantity-controls">
              <button type="button" class="quantity-btn quantity-btn--minus" data-action="decrease" data-item-id="${item.product_id}">-</button>
              <input type="text"
                     inputmode="numeric"
                     pattern="[0-9]*"
                     class="quantity-input"
                     value="${item.quantity}"
                     data-min="1"
                     data-max="${qtyMax}"
                     data-item-id="${item.product_id}">
              <button type="button" class="quantity-btn quantity-btn--plus" data-action="increase" data-item-id="${item.product_id}">+</button>
            </div>

            <div class="item-buttons">
              <button class="btn btn--secondary btn--small btn--danger" data-action="remove" data-item-id="${item.product_id}">
                Remove
              </button>
            </div>
          </div>
        </div>
      `;
      frag.appendChild(article);
    });

    container.appendChild(frag);
    this.updateCounts();
  }

  renderEmptyState() {
    const container = document.getElementById('cart-items');
    if (!container) return;
    container.innerHTML = `
      <div class="empty-state">
        <h3 class="empty-title">Your cart is empty</h3>
        <p class="empty-description">Browse the marketplace and add items to your cart.</p>
        <a href="/dashboard/marketplace/merch-store" class="btn btn--primary">Go to Marketplace</a>
      </div>
    `;
    const det = document.getElementById('summary-details');
    if (det) {
      det.innerHTML = `
        <div class="summary-line summary-line--total">
          <span class="summary-label">Total</span>
          <span class="summary-value summary-value--total" id="total">Rs. 0</span>
        </div>
      `;
    }
  }

  updateCounts() {
    const countEl = document.getElementById('cart-count');
    if (!countEl) return;
    const totalItems = Array.from(this.items.values()).reduce((sum, it) => sum + (parseInt(it.quantity, 10) || 0), 0);
    countEl.textContent = `${totalItems} item${totalItems === 1 ? '' : 's'} in your cart`;
  }

  updateSummary() {
    const items = Array.from(this.items.values());
    const subtotal = items.reduce((sum, it) => sum + (Number(it.price) * Number(it.quantity)), 0);

    const det = document.getElementById('summary-details') || document.querySelector('.summary-details');
    if (det) {
      const lines = items.map(it => {
        const lineTotal = Number(it.price) * Number(it.quantity);
        return `
          <div class="summary-line">
            <span class="summary-label">${this.escape(it.title)} × ${Number(it.quantity)}</span>
            <span class="summary-value">Rs. ${Number(lineTotal).toLocaleString()}</span>
          </div>
        `;
      }).join('');

      det.innerHTML = `
        ${lines || ''}
        ${items.length ? '<hr class="summary-divider">' : ''}
        <div class="summary-line summary-line--total">
          <span class="summary-label">Total</span>
          <span class="summary-value summary-value--total" id="total">Rs. ${Number(subtotal).toLocaleString()}</span>
        </div>
      `;
    } else {
      const totalEl = document.getElementById('total');
      if (totalEl) totalEl.textContent = `Rs. ${Number(subtotal).toLocaleString()}`;
    }
  }

  escape(s) {
    return String(s ?? '')
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new CartManager();
});