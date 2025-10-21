/**
 * Minimal Checkout JS
 * - No delivery options or extra info
 * - No delivery charges
 * - Slip upload for preorder items
 * - Place order when all preorder slips uploaded
 */
class CheckoutManager {
  constructor() {
    this.cartItems = [];
    this.codItems = [];
    this.preorderItems = [];
    this.paymentSlips = new Map(); // productId -> { file, referenceNumber, item }
  }

  init() {
    this.loadCartItems();
    this.setupEventListeners();
    this.setupFileUpload();
  }

  setupEventListeners() {
    const placeBtn = document.getElementById('place-order-btn');
    placeBtn?.addEventListener('click', () => this.placeOrder());

    const confirmBtn = document.getElementById('confirm-payment-btn');
    confirmBtn?.addEventListener('click', () => this.confirmPaymentSlip());

    // ESC to close modal
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') this.closePaymentModal();
    });
  }

  setupFileUpload() {
    const fileInput = document.getElementById('payment-slip-input');
    const uploadArea = document.getElementById('upload-area');

    if (!fileInput || !uploadArea) return;

    // Click-to-open
    uploadArea.addEventListener('click', () => fileInput.click());

    // Input change
    fileInput.addEventListener('change', (e) => {
      if (e.target.files?.length > 0) {
        this.handleFileSelect(e.target.files[0]);
      }
    });

    // Drag and drop (optional)
    uploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = 'var(--secondary-color)';
      uploadArea.style.background = 'var(--surface-hover)';
    });
    uploadArea.addEventListener('dragleave', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = 'var(--border-color)';
      uploadArea.style.background = '';
    });
    uploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = 'var(--border-color)';
      uploadArea.style.background = '';
      if (e.dataTransfer.files?.length > 0) {
        this.handleFileSelect(e.dataTransfer.files[0]);
      }
    });
  }

  async loadCartItems() {
    try {
      const res = await fetch('/dashboard/marketplace/cart/get');
      const data = await res.json();

      if (!data?.success || !Array.isArray(data.items) || data.items.length === 0) {
        this.showEmptyState();
        return;
      }

      // Normalize fields
      this.cartItems = data.items.map(x => ({
        product_id: x.product_id ?? x.id,
        title: x.title ?? '',
        image: x.image ?? '',
        quantity: Number(x.quantity ?? x.qty ?? 1),
        price: Number(x.price ?? x.unit_price ?? 0),
        allowsCOD: !!(x.allowsCOD ?? x.allows_cod ?? x.cod ?? false),
        isPreorder: !!(x.isPreorder ?? x.preorder ?? false),
        condition: x.condition
      }));

      this.categorizeItems();
      this.renderItems();
      this.updateSummary();
      this.validateForm();
    } catch (err) {
      console.error('Failed to load cart items:', err);
      this.showEmptyState();
    }
  }

  categorizeItems() {
    this.codItems = this.cartItems.filter(item => item.allowsCOD);
    this.preorderItems = this.cartItems.filter(item => item.isPreorder);
  }

  renderItems() {
    if (!this.cartItems.length) {
      this.showEmptyState();
      return;
    }

    const codGroup = document.getElementById('cod-group');
    const preorderGroup = document.getElementById('preorder-group');

    if (this.codItems.length && codGroup) {
      codGroup.style.display = 'block';
      this.renderCODItems();
    }
    if (this.preorderItems.length && preorderGroup) {
      preorderGroup.style.display = 'block';
      this.renderPreorderItems();
    }
  }

  renderCODItems() {
    const container = document.getElementById('cod-items');
    if (!container) return;
    container.innerHTML = this.codItems.map(item => `
      <div class="checkout-item">
        <div class="checkout-item-image">
          <img src="${this.esc(item.image)}" alt="${this.esc(item.title)}" onerror="this.src='/images/placeholder-item.jpg'">
        </div>
        <div class="checkout-item-details">
          <div class="checkout-item-title">${this.esc(item.title)}</div>
          <div class="checkout-item-meta">
            Quantity: ${item.quantity} | ${item.condition === 'brand_new' ? 'Brand New' : 'Used'}
          </div>
          <div class="checkout-item-badge">Payment: Cash on Delivery</div>
        </div>
        <div class="checkout-item-price">
          Rs. ${(item.price * item.quantity).toFixed(2)}
        </div>
      </div>
    `).join('');
  }

  renderPreorderItems() {
    const container = document.getElementById('preorder-items');
    if (!container) return;
    container.innerHTML = this.preorderItems.map(item => {
      const uploaded = this.paymentSlips.has(item.product_id);
      return `
        <div class="checkout-item checkout-item--preorder">
          <div class="checkout-item-image">
            <img src="${this.esc(item.image)}" alt="${this.esc(item.title)}" onerror="this.src='/images/placeholder-item.jpg'">
          </div>
          <div class="checkout-item-details">
            <div class="checkout-item-title">${this.esc(item.title)}</div>
            <div class="checkout-item-meta">
              Quantity: ${item.quantity} | ${item.condition === 'brand_new' ? 'Brand New' : 'Used'}
            </div>
            <div class="checkout-item-badge">Payment: Pre-order</div>
            <div class="payment-required">
              <button class="upload-payment-btn ${uploaded ? 'uploaded' : ''}" data-pid="${item.product_id}">
                ${uploaded ? '✓ Uploaded' : 'Upload Payment Slip'}
              </button>
              ${uploaded ? '<span class="payment-status">Payment slip uploaded</span>' : ''}
            </div>
          </div>
          <div class="checkout-item-price">Rs. ${(item.price * item.quantity).toFixed(2)}</div>
        </div>
      `;
    }).join('');
  }

  // No delivery charges
  updateSummary() {
    const subtotal = this.cartItems.reduce((s, it) => s + (it.price * it.quantity), 0);
    const codAmount = this.codItems.reduce((s, it) => s + (it.price * it.quantity), 0);
    const preorderAmount = this.preorderItems.reduce((s, it) => s + (it.price * it.quantity), 0);

    const subEl = document.getElementById('checkout-subtotal');
    const totEl = document.getElementById('checkout-total');
    const codEl = document.getElementById('cod-amount');
    const preEl = document.getElementById('preorder-amount');

    if (subEl) subEl.textContent = `Rs. ${subtotal.toFixed(2)}`;
    if (totEl) totEl.textContent = `Rs. ${subtotal.toFixed(2)}`;
    if (codEl) codEl.textContent = `Rs. ${codAmount.toFixed(2)}`;
    if (preEl) preEl.textContent = `Rs. ${preorderAmount.toFixed(2)}`;
  }

  openPaymentModal(productId) {
    const item = this.preorderItems.find(i => i.product_id === productId);
    if (!item) return;
    this.currentPreorderItem = item;

    const title = document.getElementById('payment-modal-title');
    const amount = document.getElementById('transfer-amount');
    title && (title.textContent = `Payment for ${item.title}`);
    amount && (amount.textContent = `Rs. ${(item.price * item.quantity).toFixed(2)}`);

    // Reset modal inputs
    this.removePaymentSlip();

    const modal = document.getElementById('payment-modal');
    if (modal) modal.style.display = 'flex';
  }

  handleFileSelect(file) {
    const maxSize = 5 * 1024 * 1024;
    const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (file.size > maxSize) return alert('File size must be less than 5MB');
    if (!allowed.includes(file.type)) return alert('Only JPG, PNG, and WebP files are allowed');

    this.showFilePreview(file);
    const btn = document.getElementById('confirm-payment-btn');
    if (btn) btn.disabled = false;
  }

  showFilePreview(file) {
    const preview = document.getElementById('file-preview');
    const uploadArea = document.getElementById('upload-area');
    const nameEl = document.getElementById('preview-name');
    const sizeEl = document.getElementById('preview-size');

    nameEl && (nameEl.textContent = file.name);
    sizeEl && (sizeEl.textContent = this.formatFileSize(file.size));
    if (uploadArea) uploadArea.style.display = 'none';
    if (preview) preview.style.display = 'block';
  }

  removePaymentSlip() {
    const preview = document.getElementById('file-preview');
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('payment-slip-input');
    const confirmBtn = document.getElementById('confirm-payment-btn');

    if (fileInput) fileInput.value = '';
    if (preview) preview.style.display = 'none';
    if (uploadArea) uploadArea.style.display = 'block';
    if (confirmBtn) confirmBtn.disabled = true;
  }

  confirmPaymentSlip() {
    if (!this.currentPreorderItem) return;

    const file = document.getElementById('payment-slip-input')?.files?.[0];
    const referenceNumber = document.getElementById('reference-number')?.value || '';

    if (!file) return alert('Please select a payment slip');

    this.paymentSlips.set(this.currentPreorderItem.product_id, {
      file,
      referenceNumber,
      item: this.currentPreorderItem
    });

    this.renderPreorderItems();
    this.closePaymentModal();
    this.validateForm();
    alert('Payment slip uploaded successfully!');
  }

  closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    if (modal) modal.style.display = 'none';
    this.currentPreorderItem = null;
    this.removePaymentSlip();
    const ref = document.getElementById('reference-number');
    if (ref) ref.value = '';
  }

  // No delivery fields; just require slips for all preorder items
  validateForm() {
    const placeOrderBtn = document.getElementById('place-order-btn');
    if (!placeOrderBtn) return;

    const allPreorderPaid = this.preorderItems.every(item => this.paymentSlips.has(item.product_id));
    const isValid = allPreorderPaid && this.cartItems.length > 0;

    placeOrderBtn.disabled = !isValid;
  }

  async placeOrder() {
    if (this.cartItems.length === 0) {
      alert('Your cart is empty');
      return;
    }

    // Require slips for all preorder items
    const missing = this.preorderItems.filter(i => !this.paymentSlips.has(i.product_id));
    if (missing.length) {
      alert('Please upload payment slips for all pre-order items');
      return;
    }

    this.showLoading();

    try {
      const formData = new FormData();
      // No delivery or additional info
      formData.append('cart_items', JSON.stringify(this.cartItems));

      // Attach slips
      let i = 0;
      for (const [productId, pd] of this.paymentSlips) {
        formData.append(`payment_slips[${i}][product_id]`, productId);
        formData.append(`payment_slips[${i}][file]`, pd.file);
        formData.append(`payment_slips[${i}][reference_number]`, pd.referenceNumber || '');
        i++;
      }

      const res = await fetch('/dashboard/marketplace/checkout/place-order', { method: 'POST', body: formData });
      const data = await res.json();

      if (!res.ok || !data?.success) throw new Error(data?.message || 'Failed to place order');

      alert('Order placed successfully! Redirecting to orders page...');
      setTimeout(() => { window.location.href = '/dashboard/marketplace/orders'; }, 1500);
    } catch (err) {
      console.error('Place order error:', err);
      alert('Failed to place order. Please try again.');
    } finally {
      this.hideLoading();
    }
  }

  showEmptyState() {
    const container = document.querySelector('.cart-container');
    if (container) container.style.display = 'none';
    const empty = document.getElementById('empty-checkout');
    if (empty) empty.style.display = 'block';
  }

  showLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'flex';
  }

  hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) overlay.style.display = 'none';
  }

  formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024, sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${(bytes / Math.pow(k, i)).toFixed(2)} ${sizes[i]}`;
  }

  esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }
}

// Expose helpers for inline handlers in the view
function closePaymentModal() { checkoutManager?.closePaymentModal(); }
function removePaymentSlip() { checkoutManager?.removePaymentSlip(); }
function copyAccountNumber() {
  const accountNumber = document.getElementById('account-number')?.textContent?.trim() || '';
  if (!accountNumber) return;
  navigator.clipboard.writeText(accountNumber)
    .then(() => checkoutManager?.showMessage('Account number copied to clipboard!', 'success'))
    .catch(() => checkoutManager?.showMessage('Failed to copy account number', 'error'));
}

// Initialize
let checkoutManager;
document.addEventListener('DOMContentLoaded', () => {
  const $ = (id) => document.getElementById(id);
  const state = { items: [], cod: [], pre: [], slips: new Map(), current: null };

  // Elements
  const codGroup = $('cod-group'), codItems = $('cod-items');
  const preGroup = $('preorder-group'), preItems = $('preorder-items');
  const subtotalEl = $('checkout-subtotal'), totalEl = $('checkout-total');
  const codAmountEl = $('cod-amount'), preAmountEl = $('preorder-amount');
  const placeBtn = $('place-order-btn');
  const emptyState = $('empty-checkout');
  const loading = $('loading-overlay');
  const paymentModal = $('payment-modal');
  const confirmBtn = $('confirm-payment-btn');
  const fileInput = $('payment-slip-input');
  const preview = $('file-preview');
  const uploadArea = $('upload-area');
  const previewName = $('preview-name');
  const previewSize = $('preview-size');
  const transferAmount = $('transfer-amount');

  // Load items from API; rely on cart-selected payment_method
  fetch('/dashboard/marketplace/cart/get')
    .then(r => r.json())
    .then(data => {
      const raw = Array.isArray(data?.items) ? data.items : [];
      if (raw.length === 0) return showEmpty();

      state.items = raw.map(x => ({
        product_id: Number(x.product_id ?? x.id),
        title: x.title ?? '',
        image: x.image ?? '',
        quantity: Number(x.quantity ?? x.qty ?? 1),
        price: Number(x.price ?? x.unit_price ?? 0),
        // allowed flags (for info), but we only DISPLAY by selected payment_method
        allowsCOD: !!(x.allowsCOD ?? x.allows_cod),
        allowsPre: !!(x.isPreorder ?? x.preorder),
        payment_method: (x.payment_method === 'preorder' || x.payment_method === 'cash_on_delivery')
          ? x.payment_method
          // fallback: if API didn’t send selected method, derive a best guess
          : ((x.allowsCOD ?? x.allows_cod) ? 'cash_on_delivery' : 'preorder'),
        condition: x.condition
      }));

      categorize();
      render();
      updateSummary();
      validate();
    })
    .catch(() => showEmpty());

  function categorize() {
    state.cod = state.items.filter(i => i.payment_method === 'cash_on_delivery');
    state.pre = state.items.filter(i => i.payment_method === 'preorder');
  }

  function render() {
    // COD group
    if (codGroup && codItems) {
      if (state.cod.length) {
        codGroup.style.display = 'block';
        codItems.innerHTML = state.cod.map(item => `
          <div class="checkout-item">
            <div class="checkout-item-image">
              <img src="${esc(item.image)}" alt="${esc(item.title)}" onerror="this.src='/images/placeholder-item.jpg'">
            </div>
            <div class="checkout-item-details">
              <div class="checkout-item-title">${esc(item.title)}</div>
              <div class="checkout-item-meta">
                Quantity: ${item.quantity} | ${item.condition === 'brand_new' ? 'Brand New' : 'Used'}
              </div>
              <div class="checkout-item-badge">Payment: Cash on Delivery</div>
            </div>
            <div class="checkout-item-price">Rs. ${(item.price * item.quantity).toFixed(2)}</div>
          </div>
        `).join('');
      } else {
        codGroup.style.display = 'none';
        codItems.innerHTML = '';
      }
    }

    // Preorder group
    if (preGroup && preItems) {
      if (state.pre.length) {
        preGroup.style.display = 'block';
        preItems.innerHTML = state.pre.map(item => {
          const up = state.slips.has(item.product_id);
          return `
            <div class="checkout-item checkout-item--preorder">
              <div class="checkout-item-image">
                <img src="${esc(item.image)}" alt="${esc(item.title)}" onerror="this.src='/images/placeholder-item.jpg'">
              </div>
              <div class="checkout-item-details">
                <div class="checkout-item-title">${esc(item.title)}</div>
                <div class="checkout-item-meta">
                  Quantity: ${item.quantity} | ${item.condition === 'brand_new' ? 'Brand New' : 'Used'}
                </div>
                <div class="checkout-item-badge">Payment: Pre-order</div>
                <div class="payment-required">
                  <button class="upload-payment-btn ${up ? 'uploaded' : ''}" data-pid="${item.product_id}">
                    ${up ? '✓ Uploaded' : 'Upload Payment Slip'}
                  </button>
                  ${up ? '<span class="payment-status">Payment slip uploaded</span>' : ''}
                </div>
              </div>
              <div class="checkout-item-price">Rs. ${(item.price * item.quantity).toFixed(2)}</div>
            </div>
          `;
        }).join('');
      } else {
        preGroup.style.display = 'none';
        preItems.innerHTML = '';
      }
    }
  }

  // Totals: no delivery fee
  function updateSummary() {
    const subtotal = sum(state.items);
    const codAmt = sum(state.cod);
    const preAmt = sum(state.pre);
    if (subtotalEl) subtotalEl.textContent = `Rs. ${subtotal.toFixed(2)}`;
    if (totalEl) totalEl.textContent = `Rs. ${subtotal.toFixed(2)}`;
    if (codAmountEl) codAmountEl.textContent = `Rs. ${codAmt.toFixed(2)}`;
    if (preAmountEl) preAmountEl.textContent = `Rs. ${preAmt.toFixed(2)}`;
  }

  function validate() {
    const allPrePaid = state.pre.every(i => state.slips.has(i.product_id));
    if (placeBtn) placeBtn.disabled = !(state.items.length > 0 && allPrePaid);
  }

  // Modal open for preorder upload
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.upload-payment-btn');
    if (!btn) return;
    const pid = Number(btn.dataset.pid);
    const item = state.pre.find(i => i.product_id === pid);
    if (!item) return;
    state.current = item;
    const ttl = $('payment-modal-title');
    if (ttl) ttl.textContent = `Payment for ${item.title}`;
    if (transferAmount) transferAmount.textContent = `Rs. ${(item.price * item.quantity).toFixed(2)}`;
    if (fileInput) fileInput.value = '';
    if (preview) preview.style.display = 'none';
    if (uploadArea) uploadArea.style.display = 'block';
    if (confirmBtn) confirmBtn.disabled = true;
    if (paymentModal) paymentModal.style.display = 'flex';
  });

  // File select
  if (fileInput) {
    fileInput.addEventListener('change', () => {
      const f = fileInput.files?.[0]; if (!f) return;
      const okType = ['image/jpeg','image/jpg','image/png','image/webp'].includes(f.type);
      const okSize = f.size <= 5 * 1024 * 1024;
      if (!okType) return alert('Only JPG, PNG, WebP allowed');
      if (!okSize) return alert('File must be under 5MB');
      if (previewName) previewName.textContent = f.name;
      if (previewSize) previewSize.textContent = formatSize(f.size);
      if (uploadArea) uploadArea.style.display = 'none';
      if (preview) preview.style.display = 'block';
      if (confirmBtn) confirmBtn.disabled = false;
    });
  }

  // Confirm slip
  if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
      if (!state.current) return;
      const f = fileInput?.files?.[0]; if (!f) return alert('Please select a payment slip');
      const ref = ($('reference-number')?.value || '').trim();
      state.slips.set(state.current.product_id, { file: f, reference: ref });
      if (paymentModal) paymentModal.style.display = 'none';
      render(); validate();
      alert('Payment slip uploaded successfully!');
    });
  }

  // Place order
  if (placeBtn) {
    placeBtn.addEventListener('click', async () => {
      const allPrePaid = state.pre.every(i => state.slips.has(i.product_id));
      if (!allPrePaid) return alert('Please upload payment slips for all pre-order items');
      showLoading(true);
      try {
        const fd = new FormData();
        fd.append('cart_items', JSON.stringify(state.items));
        let i = 0;
        state.slips.forEach((data, pid) => {
          fd.append(`payment_slips[${i}][product_id]`, pid);
          fd.append(`payment_slips[${i}][file]`, data.file);
          fd.append(`payment_slips[${i}][reference_number]`, data.reference || '');
          i++;
        });
        const res = await fetch('/dashboard/marketplace/checkout/place-order', { method: 'POST', body: fd });
        const json = await res.json().catch(()=>({}));
        if (!res.ok || !json?.success) throw new Error(json?.message || 'Failed to place order');
        alert('Order placed successfully! Redirecting...');
        location.href = '/dashboard/marketplace/orders';
      } catch (e) {
        alert(e.message || 'Order failed');
      } finally {
        showLoading(false);
      }
    });
  }

  function showEmpty() {
    const container = document.querySelector('.cart-container');
    if (container) container.style.display = 'none';
    if (emptyState) emptyState.style.display = 'block';
  }
  function showLoading(show) { if (loading) loading.style.display = show ? 'flex' : 'none'; }
  function sum(items) { return items.reduce((s, i) => s + (i.price * i.quantity), 0); }
  function esc(s) { return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
  function formatSize(b){ if(!b)return'0 Bytes'; const k=1024,i=Math.floor(Math.log(b)/Math.log(k)); return `${(b/Math.pow(k,i)).toFixed(2)} ${['Bytes','KB','MB','GB'][i]}`; }
});
