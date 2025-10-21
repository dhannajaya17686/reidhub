<link rel="stylesheet" href="/css/app/user/marketplace/my-cart.css">
<link rel="stylesheet" href="/css/app/user/marketplace/checkout.css">
<main class="cart-main" role="main" aria-label="Checkout">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item"><a href="/dashboard/marketplace/merch-store" class="breadcrumb__link">Marketplace</a></li>
      <li class="breadcrumb__item"><a href="/dashboard/marketplace/my-cart" class="breadcrumb__link">Cart</a></li>
      <li class="breadcrumb__item breadcrumb__item--current">Checkout</li>
    </ol>
  </nav>

  <header class="page-header">
    <h1 class="page-title">Checkout</h1>
    <p class="cart-count">Complete your order</p>
  </header>

  <div class="cart-container">
    <div class="cart-items">
      <div class="checkout-group" id="cod-group" style="display:none;">
        <h2 class="checkout-group-title">Cash on Delivery Items</h2>
        <div class="checkout-items" id="cod-items"></div>
      </div>

      <div class="checkout-group" id="preorder-group" style="display:none;">
        <h2 class="checkout-group-title">Pre-order Items</h2>
        <p class="checkout-group-subtitle">Please upload payment slips for these items</p>
        <div class="checkout-items" id="preorder-items"></div>
      </div>

    
    </div>

    <div class="order-summary">
      <div class="summary-card">
        <h3 class="summary-title">Order Summary</h3>
        <div class="summary-details">
          <div class="summary-line">
            <span class="summary-label">Subtotal:</span>
            <span class="summary-value" id="checkout-subtotal">Rs. 0.00</span>
          </div>
          <div class="summary-line">
            <span class="summary-label">Delivery Fee:</span>
            <span class="summary-value">Rs. 200.00</span>
          </div>
          <hr class="summary-divider">
          <div class="summary-line summary-line--total">
            <span class="summary-label">Total:</span>
            <span class="summary-value summary-value--total" id="checkout-total">Rs. 200.00</span>
          </div>
        </div>

        <div class="payment-summary">
          <h4 style="margin-bottom: var(--space-md); font-weight: 600;">Payment Breakdown</h4>
          <div class="payment-split">
            <div class="split-item">
              <span class="split-label">Cash on Delivery:</span>
              <span class="split-value" id="cod-amount">Rs. 0.00</span>
            </div>
            <div class="split-item">
              <span class="split-label">Pre-order Payment:</span>
              <span class="split-value" id="preorder-amount">Rs. 0.00</span>
            </div>
          </div>
        </div>

        <div class="checkout-section">
          <button type="button" class="btn btn--primary btn--large btn--full-width" id="place-order-btn" disabled>
            Place Order
          </button>
          
          <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: var(--space-sm);">
            By placing this order, you agree to our terms and conditions.
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="empty-state" id="empty-checkout" style="display:none;">
    <div style="text-align:center;padding:var(--space-3xl);">
      <svg width="64" height="64" viewBox="0 0 24 24" fill="none" style="color:var(--text-muted);margin-bottom:var(--space-lg);">
        <path d="M7 18a2 2 0 1 0 2 2 2 2 0 0 0-2-2Zm10 0a2 2 0 1 0 2 2 2 2 0 0 0-2-2ZM3 4h2l2.7 9.4A2 2 0 0 0 9.6 14h6.9a2 2 0 0 0 1.9-1.5L21 7H6" stroke="currentColor" stroke-width="2"/>
      </svg>
      <h3 style="font-size:1.25rem;font-weight:600;color:var(--text-primary);margin-bottom:var(--space-sm);">No Items to Checkout</h3>
      <p style="color:var(--text-secondary);margin-bottom:var(--space-lg);">Your cart is empty. Add some items before proceeding to checkout.</p>
      <a href="/dashboard/marketplace/merch-store" class="btn btn--primary">Continue Shopping</a>
    </div>
  </div>
</main>

<!-- Payment Slip Upload Modal -->
<div class="modal-overlay" id="payment-modal" style="display:none;">
  <div class="modal modal--large">
    <div class="modal-header">
      <h3 class="modal-title" id="payment-modal-title">Upload Payment Slip</h3>
      <button class="modal-close" onclick="closePaymentModal()">×</button>
    </div>
    <div class="modal-body">
      <div class="bank-info" id="bank-info">
        <h4 class="bank-title">Bank Transfer Details</h4>
        <div class="bank-details">
          <div class="bank-item"><span class="bank-label">Bank Name:</span><span class="bank-value" id="bank-name">Commercial Bank of Ceylon</span></div>
          <div class="bank-item"><span class="bank-label">Branch:</span><span class="bank-value" id="bank-branch">Nugegoda</span></div>
          <div class="bank-item"><span class="bank-label">Account Name:</span><span class="bank-value" id="account-name">John Doe</span></div>
          <div class="bank-item">
            <span class="bank-label">Account Number:</span>
            <span class="bank-value" id="account-number">
              8001234567890
              <button class="copy-btn" onclick="copyAccountNumber()" title="Copy Account Number">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" stroke="currentColor" stroke-width="2"/>
                </svg>
              </button>
            </span>
          </div>
          <div class="bank-item bank-item--amount"><span class="bank-label">Amount to Transfer:</span><span class="bank-value bank-value--highlight" id="transfer-amount">Rs. 0.00</span></div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Payment Slip *</label>
        <div class="file-upload-area" id="upload-area">
          <input type="file" id="payment-slip-input" class="file-input" accept="image/*" required>
          <div class="file-upload-content">
            <h4 class="upload-title">Upload Payment Slip</h4>
            <p class="upload-subtitle">Click to select image (JPG, PNG, WebP - Max 5MB)</p>
          </div>
        </div>

        <div class="file-preview" id="file-preview" style="display:none;">
          <div class="preview-content">
            <div class="preview-details">
              <div class="preview-name" id="preview-name">payment-slip.jpg</div>
              <div class="preview-size" id="preview-size">2.3 MB</div>
            </div>
            <button class="preview-remove" onclick="removePaymentSlip()">Remove</button>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="reference-number" class="form-label">Reference Number (Optional)</label>
        <input type="text" id="reference-number" name="reference_number" class="form-input" placeholder="Transaction reference number">
      </div>

      <div class="modal-actions">
        <button class="btn btn--secondary" onclick="closePaymentModal()">Cancel</button>
        <button class="btn btn--primary" id="confirm-payment-btn" disabled>Confirm Payment</button>
      </div>
    </div>
  </div>
</div>

<!-- Loading Overlay -->
<div class="modal-overlay" id="loading-overlay" style="display:none;">
  <div style="text-align:center;color:white;">
    <div style="width:40px;height:40px;border:3px solid rgba(255,255,255,0.3);border-top:3px solid white;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto var(--space-md);"></div>
    <p>Processing your order...</p>
  </div>
</div>

<script src="/js/app/marketplace/checkout.js"></script>