<link rel="stylesheet" href="/css/app/user/marketplace/my-cart.css">

<!-- Main Content Area -->
<main class="cart-main" role="main" aria-label="Shopping Cart">
  
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/marketplace" class="breadcrumb__link">Marketplace</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        My Cart
      </li>
    </ol>
  </nav>

  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">My Cart</h1>
    <div class="cart-count">2 items in your cart</div>
  </div>

  <!-- Cart Content -->
  <div class="cart-container">
    
    <!-- Cart Items Section -->
    <section class="cart-items" aria-label="Cart Items">
      
      <!-- Cart Item 1 -->
      <article class="cart-item" data-item-id="1">
        <div class="item-image">
          <img src="https://via.placeholder.com/120x120/1e3a8a/ffffff?text=UCSC+Tshirt" alt="UCSC Tshirt">
          <div class="stock-badge stock-badge--in-stock">In Stock</div>
        </div>
        
        <div class="item-details">
          <div class="item-header">
            <h3 class="item-title">UCSC Tshirt</h3>
            <div class="item-price">Rs. 2,000</div>
          </div>
          
          <div class="item-meta">
            <div class="item-condition">
              Condition: <span class="condition-badge condition-badge--new">Brand New</span>
            </div>
            <div class="item-seller">
              Sold by: <span class="seller-name">Students Union of UCSC</span>
            </div>
          </div>
          
          <!-- Payment Options -->
          <div class="payment-options">
            <div class="payment-option">
              <input type="radio" id="cod-1" name="payment-1" value="cod" class="payment-radio" checked>
              <label for="cod-1" class="payment-label">
                <span class="payment-icon">🚚</span>
                Cash on Delivery
              </label>
            </div>
            <div class="payment-option">
              <input type="radio" id="prepaid-1" name="payment-1" value="prepaid" class="payment-radio">
              <label for="prepaid-1" class="payment-label">
                <span class="payment-icon">💳</span>
                Pay Upfront
                <span class="discount-badge">5% off</span>
              </label>
            </div>
          </div>
          
          <div class="item-actions">
            <div class="quantity-controls">
              <button type="button" class="quantity-btn quantity-btn--minus" data-action="decrease" data-item-id="1">-</button>
              <input type="number" class="quantity-input" value="2" min="1" max="10" data-item-id="1">
              <button type="button" class="quantity-btn quantity-btn--plus" data-action="increase" data-item-id="1">+</button>
            </div>
            
            <div class="item-buttons">
              <button class="btn btn--secondary btn--small" data-action="save-later" data-item-id="1">
                Save for Later
              </button>
              <button class="btn btn--secondary btn--small btn--danger" data-action="remove" data-item-id="1">
                Remove
              </button>
            </div>
          </div>
        </div>
      </article>

      <!-- Cart Item 2 -->
      <article class="cart-item" data-item-id="2">
        <div class="item-image">
          <img src="https://via.placeholder.com/120x120/374151/ffffff?text=Wrist+Band" alt="UCSC Wrist Band">
          <div class="stock-badge stock-badge--preorder">Pre-order</div>
        </div>
        
        <div class="item-details">
          <div class="item-header">
            <h3 class="item-title">UCSC Wrist Band</h3>
            <div class="item-price">Rs. 600</div>
          </div>
          
          <div class="item-meta">
            <div class="item-condition">
              Condition: <span class="condition-badge condition-badge--new">Brand New</span>
            </div>
            <div class="item-seller">
              Sold by: <span class="seller-name">Students Union of UCSC</span>
            </div>
            <div class="preorder-note">
              <span class="preorder-icon">⏰</span>
              Pre-order item - Payment required upfront
            </div>
          </div>
          
          <!-- Payment Options - Pre-order only -->
          <div class="payment-options">
            <div class="payment-option payment-option--disabled">
              <input type="radio" id="cod-2" name="payment-2" value="cod" class="payment-radio" disabled>
              <label for="cod-2" class="payment-label payment-label--disabled">
                <span class="payment-icon">🚚</span>
                Cash on Delivery
                <span class="unavailable-text">(Not available for pre-orders)</span>
              </label>
            </div>
            <div class="payment-option">
              <input type="radio" id="prepaid-2" name="payment-2" value="prepaid" class="payment-radio" checked>
              <label for="prepaid-2" class="payment-label">
                <span class="payment-icon">💳</span>
                Pay Upfront
                <span class="required-badge">Required</span>
              </label>
            </div>
          </div>
          
          <div class="item-actions">
            <div class="quantity-controls">
              <button type="button" class="quantity-btn quantity-btn--minus" data-action="decrease" data-item-id="2">-</button>
              <input type="number" class="quantity-input" value="1" min="1" max="5" data-item-id="2">
              <button type="button" class="quantity-btn quantity-btn--plus" data-action="increase" data-item-id="2">+</button>
            </div>
            
            <div class="item-buttons">
              <button class="btn btn--secondary btn--small" data-action="save-later" data-item-id="2">
                Save for Later
              </button>
              <button class="btn btn--secondary btn--small btn--danger" data-action="remove" data-item-id="2">
                Remove
              </button>
            </div>
          </div>
        </div>
      </article>
      
    </section>

    <!-- Order Summary Section -->
    <aside class="order-summary" aria-label="Order Summary">
      <div class="summary-card">
        <h2 class="summary-title">Order Summary</h2>
        
        <div class="summary-details">
          <div class="summary-line">
            <span class="summary-label">Subtotal (2 items)</span>
            <span class="summary-value" id="subtotal">Rs. 4,600</span>
          </div>
          
          <div class="summary-line">
            <span class="summary-label">Shipping</span>
            <span class="summary-value" id="shipping">Rs. 300</span>
          </div>
          
          <div class="summary-line">
            <span class="summary-label">Taxes</span>
            <span class="summary-value" id="taxes">Rs. 100</span>
          </div>
          
          <div class="summary-line summary-line--discount" id="discount-line" style="display: none;">
            <span class="summary-label">Prepayment Discount</span>
            <span class="summary-value summary-value--discount" id="discount">-Rs. 100</span>
          </div>
          
          <hr class="summary-divider">
          
          <div class="summary-line summary-line--total">
            <span class="summary-label">Total</span>
            <span class="summary-value summary-value--total" id="total">Rs. 5,000</span>
          </div>
        </div>
        
        <div class="checkout-section">
          <button class="btn btn--primary btn--large btn--full-width" id="checkout-btn">
            Proceed to Checkout
          </button>
          
          <div class="payment-summary" id="payment-summary">
            <div class="payment-split">
              <div class="split-item">
                <span class="split-label">Cash on Delivery:</span>
                <span class="split-value" id="cod-amount">Rs. 4,000</span>
              </div>
              <div class="split-item">
                <span class="split-label">Pay Upfront:</span>
                <span class="split-value" id="prepaid-amount">Rs. 600</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </aside>
    
  </div>

  <!-- Payment Modal -->
  <div class="modal-overlay" id="payment-modal" role="dialog" aria-labelledby="payment-title" aria-modal="true" style="display: none;">
    <div class="modal modal--large">
      <div class="modal-header">
        <h2 id="payment-title" class="modal-title">Complete Payment</h2>
        <button class="modal-close" aria-label="Close payment modal">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
      
      <div class="modal-body">
        <!-- Bank Information -->
        <div class="bank-info">
          <h3 class="bank-title">Bank Transfer Details</h3>
          <div class="bank-details">
            <div class="bank-item">
              <span class="bank-label">Bank Name:</span>
              <span class="bank-value">Commercial Bank of Ceylon</span>
            </div>
            <div class="bank-item">
              <span class="bank-label">Account Name:</span>
              <span class="bank-value">Students Union of UCSC</span>
            </div>
            <div class="bank-item">
              <span class="bank-label">Account Number:</span>
              <span class="bank-value">8001234567890</span>
              <button class="copy-btn" data-copy="8001234567890" title="Copy account number">
                <svg width="16" height="16" fill="none" viewBox="0 0 16 16">
                  <path d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V2Z" stroke="currentColor" stroke-width="1.5"/>
                  <path d="M2 6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                </svg>
              </button>
            </div>
            <div class="bank-item">
              <span class="bank-label">Branch:</span>
              <span class="bank-value">Nugegoda</span>
            </div>
            <div class="bank-item bank-item--amount">
              <span class="bank-label">Amount to Transfer:</span>
              <span class="bank-value bank-value--highlight" id="transfer-amount">Rs. 600</span>
            </div>
          </div>
        </div>
        
        <!-- Payment Form -->
        <form class="payment-form" id="payment-form">
          <div class="form-group">
            <label for="payment-slip" class="form-label">Upload Payment Slip *</label>
            <div class="file-upload-area" id="file-upload-area">
              <input type="file" id="payment-slip" name="payment-slip" accept="image/*,.pdf" class="file-input" required>
              <div class="file-upload-content">
                <div class="upload-icon">
                  <svg width="48" height="48" fill="none" viewBox="0 0 24 24">
                    <path d="M12 16V8m0 0l-3 3m3-3l3 3M9 21h6a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0010.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="upload-text">
                  <p class="upload-title">Click to upload payment slip</p>
                  <p class="upload-subtitle">PNG, JPG, PDF up to 10MB</p>
                </div>
              </div>
            </div>
            <div class="file-preview" id="file-preview" style="display: none;">
              <div class="preview-content">
                <div class="preview-icon">
                  <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="preview-details">
                  <div class="preview-name" id="preview-name"></div>
                  <div class="preview-size" id="preview-size"></div>
                </div>
                <button type="button" class="preview-remove" id="preview-remove">
                  <svg width="20" height="20" fill="none" viewBox="0 0 20 20">
                    <path d="M6 6l8 8M6 14l8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="reference-number" class="form-label">Reference Number (Optional)</label>
            <input type="text" id="reference-number" name="reference-number" class="form-input" placeholder="Enter bank reference number">
          </div>
          
          <div class="form-group">
            <label for="payment-notes" class="form-label">Additional Notes (Optional)</label>
            <textarea id="payment-notes" name="payment-notes" class="form-textarea" rows="3" placeholder="Any additional information about your payment..."></textarea>
          </div>
          
          <div class="modal-actions">
            <button type="button" class="btn btn--secondary" id="cancel-payment">Cancel</button>
            <button type="submit" class="btn btn--primary">Submit Payment</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</main>

<!-- JavaScript -->
<script type="module" src="/js/app/marketplace/my-cart.js"></script>
</body>
</html>