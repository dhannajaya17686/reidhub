<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-add-items.css">

<!-- Main Add Items Form -->
<main class="add-items-main" role="main" aria-label="Add New Item Form">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Add Items</h1>
      <p class="page-subtitle">Fill in the details below to add a new item to your store.</p>
    </div>
  </div>

  <!-- Add Item Form -->
  <form class="add-item-form" id="add-item-form" method="POST" action="/dashboard/marketplace/seller/add" enctype="multipart/form-data">
    
    <!-- Basic Information Section -->
    <div class="form-section">
      <h2 class="section-title">Basic Information</h2>
      
      <div class="form-grid">
        <!-- Item Name -->
        <div class="form-group">
          <label for="item-name" class="form-label">Item Name *</label>
          <input 
            type="text" 
            id="item-name" 
            name="item_name" 
            class="form-input" 
            placeholder="e.g., Jersey" 
            required
            maxlength="100"
          >
          <div class="form-error" id="item-name-error"></div>
        </div>

        <!-- Category -->
        <div class="form-group">
          <label for="category" class="form-label">Category *</label>
          <select id="category" name="category" class="form-select" required>
            <option value="">Select Category</option>
            <option value="apparel">Apparel</option>
            <option value="accessories">Accessories</option>
            <option value="stationery">Stationery</option>
            <option value="electronics">Electronics</option>
            <option value="books">Books</option>
            <option value="other">Other</option>
          </select>
          <div class="form-error" id="category-error"></div>
        </div>
      </div>

      <!-- Condition -->
      <div class="form-group">
        <label class="form-label">Condition *</label>
        <div class="radio-group">
          <label class="radio-option">
            <input type="radio" name="condition" value="brand_new" required>
            <span class="radio-custom"></span>
            <span class="radio-text">Brand New</span>
          </label>
          <label class="radio-option">
            <input type="radio" name="condition" value="used" required>
            <span class="radio-custom"></span>
            <span class="radio-text">Used</span>
          </label>
        </div>
        <div class="form-error" id="condition-error"></div>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label for="description" class="form-label">Description *</label>
        <textarea 
          id="description" 
          name="description" 
          class="form-textarea" 
          placeholder="Describe your item in detail..."
          rows="4"
          required
          maxlength="500"
        ></textarea>
        <div class="char-counter">
          <span id="desc-count">0</span>/500 characters
        </div>
        <div class="form-error" id="description-error"></div>
      </div>
    </div>

    <!-- Pricing & Inventory Section -->
    <div class="form-section">
      <h2 class="section-title">Pricing & Inventory</h2>
      
      <div class="form-grid">
        <!-- Item Price -->
        <div class="form-group">
          <label for="item-price" class="form-label">Item Price (Rs.) *</label>
          <div class="price-input">
            <span class="price-prefix">Rs.</span>
            <input 
              type="number" 
              id="item-price" 
              name="item_price" 
              class="form-input price-field" 
              placeholder="2000" 
              min="1"
              max="999999"
              step="1"
              required
            >
          </div>
          <div class="form-error" id="item-price-error"></div>
        </div>

        <!-- Item Quantity -->
        <div class="form-group">
          <label for="item-quantity" class="form-label">Item Quantity *</label>
          <div class="quantity-input">
            <button type="button" class="qty-btn qty-decrease" data-target="item-quantity">-</button>
            <input 
              type="number" 
              id="item-quantity" 
              name="item_quantity" 
              class="form-input qty-field" 
              value="1"
              min="1"
              max="999"
              required
            >
            <button type="button" class="qty-btn qty-increase" data-target="item-quantity">+</button>
          </div>
          <div class="form-error" id="item-quantity-error"></div>
        </div>
      </div>
    </div>

    <!-- Payment & Delivery Options Section -->
    <div class="form-section">
      <h2 class="section-title">Payment & Delivery Options</h2>
      <p class="section-subtitle">Select the payment methods you want to accept for this item.</p>
      
      <!-- Payment Options -->
      <div class="form-group">
        <label class="form-label">Accepted Payment Methods *</label>
        <div class="checkbox-group">
          <label class="checkbox-option">
            <input type="checkbox" name="payment_methods[]" value="cash_on_delivery" id="cod-option">
            <span class="checkbox-custom"></span>
            <div class="checkbox-content">
              <span class="checkbox-title">Cash on Delivery</span>
              <span class="checkbox-description">Customer pays when they receive the item</span>
            </div>
          </label>
          
          <label class="checkbox-option">
            <input type="checkbox" name="payment_methods[]" value="preorder" id="preorder-option">
            <span class="checkbox-custom"></span>
            <div class="checkbox-content">
              <span class="checkbox-title">Pre-order / Upfront Payment</span>
              <span class="checkbox-description">Customer pays before receiving the item</span>
            </div>
          </label>
        </div>
        <div class="form-error" id="payment-methods-error"></div>
      </div>

      <!-- Bank Account Details (shown when preorder is selected) -->
      <div class="bank-details-section" id="bank-details-section" style="display: none;">
        <h3 class="subsection-title">Bank Account Details</h3>
        <p class="subsection-subtitle">Provide your bank account details for receiving payments.</p>
        
        <div class="form-grid">
          <!-- Bank Name -->
          <div class="form-group">
            <label for="bank-name" class="form-label">Bank Name *</label>
            <select id="bank-name" name="bank_name" class="form-select">
              <option value="">Select Bank</option>
              <option value="commercial_bank">Commercial Bank of Ceylon</option>
              <option value="peoples_bank">People's Bank</option>
              <option value="bank_of_ceylon">Bank of Ceylon</option>
              <option value="hatton_national">Hatton National Bank</option>
              <option value="sampath_bank">Sampath Bank</option>
              <option value="seylan_bank">Seylan Bank</option>
              <option value="dfcc_bank">DFCC Bank</option>
              <option value="ndb_bank">National Development Bank</option>
              <option value="nations_trust">Nations Trust Bank</option>
              <option value="union_bank">Union Bank</option>
              <option value="other">Other</option>
            </select>
            <div class="form-error" id="bank-name-error"></div>
          </div>

          <!-- Branch -->
          <div class="form-group">
            <label for="bank-branch" class="form-label">Branch *</label>
            <input 
              type="text" 
              id="bank-branch" 
              name="bank_branch" 
              class="form-input" 
              placeholder="e.g., Nugegoda"
            >
            <div class="form-error" id="bank-branch-error"></div>
          </div>
        </div>

        <div class="form-grid">
          <!-- Account Name -->
          <div class="form-group">
            <label for="account-name" class="form-label">Account Holder Name *</label>
            <input 
              type="text" 
              id="account-name" 
              name="account_name" 
              class="form-input" 
              placeholder="e.g., John Doe"
            >
            <div class="form-error" id="account-name-error"></div>
          </div>

          <!-- Account Number -->
          <div class="form-group">
            <label for="account-number" class="form-label">Account Number *</label>
            <input 
              type="text" 
              id="account-number" 
              name="account_number" 
              class="form-input" 
              placeholder="e.g., 8001234567890"
              pattern="[0-9]{10,18}"
              title="Please enter a valid account number (10-18 digits)"
            >
            <div class="form-error" id="account-number-error"></div>
          </div>
        </div>

        <!-- Bank Account Preview -->
        <div class="bank-preview" id="bank-preview" style="display: none;">
          <h4 class="preview-title">Bank Account Preview</h4>
          <div class="preview-details">
            <div class="preview-item">
              <span class="preview-label">Bank:</span>
              <span class="preview-value" id="preview-bank">-</span>
            </div>
            <div class="preview-item">
              <span class="preview-label">Branch:</span>
              <span class="preview-value" id="preview-branch">-</span>
            </div>
            <div class="preview-item">
              <span class="preview-label">Account Name:</span>
              <span class="preview-value" id="preview-account-name">-</span>
            </div>
            <div class="preview-item">
              <span class="preview-label">Account Number:</span>
              <span class="preview-value" id="preview-account-number">-</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Images Section -->
    <div class="form-section">
      <h2 class="section-title">Upload Images</h2>
      <p class="section-subtitle">Upload up to 4 images. The first image will be the main image.</p>
      
      <div class="image-upload-container">
        <!-- Main Image -->
        <div class="image-slot main-image" data-slot="0">
          <input type="file" id="image-0" name="images[]" accept="image/*" class="image-input" hidden>
          <div class="image-preview" id="preview-0">
            <div class="upload-placeholder">
              <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span class="upload-text">Main Image</span>
              <span class="upload-hint">Click to upload</span>
            </div>
          </div>
          <button type="button" class="remove-image" style="display: none;">×</button>
          <div class="main-badge">MAIN</div>
        </div>

        <!-- Sub Images -->
        <div class="image-slot" data-slot="1">
          <input type="file" id="image-1" name="images[]" accept="image/*" class="image-input" hidden>
          <div class="image-preview" id="preview-1">
            <div class="upload-placeholder">
              <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span class="upload-text">Image 2</span>
              <span class="upload-hint">Optional</span>
            </div>
          </div>
          <button type="button" class="remove-image" style="display: none;">×</button>
        </div>

        <div class="image-slot" data-slot="2">
          <input type="file" id="image-2" name="images[]" accept="image/*" class="image-input" hidden>
          <div class="image-preview" id="preview-2">
            <div class="upload-placeholder">
              <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span class="upload-text">Image 3</span>
              <span class="upload-hint">Optional</span>
            </div>
          </div>
          <button type="button" class="remove-image" style="display: none;">×</button>
        </div>

        <div class="image-slot" data-slot="3">
          <input type="file" id="image-3" name="images[]" accept="image/*" class="image-input" hidden>
          <div class="image-preview" id="preview-3">
            <div class="upload-placeholder">
              <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span class="upload-text">Image 4</span>
              <span class="upload-hint">Optional</span>
            </div>
          </div>
          <button type="button" class="remove-image" style="display: none;">×</button>
        </div>
      </div>

      <div class="upload-tips">
        <h4>Image Guidelines:</h4>
        <ul>
          <li>Maximum file size: 5MB per image</li>
          <li>Supported formats: JPG, PNG, WebP</li>
          <li>Recommended size: 800x800px or larger</li>
          <li>Use clear, well-lit photos</li>
        </ul>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
      <button type="button" class="btn btn-secondary" onclick="history.back()">
        Cancel
      </button>
      <button type="submit" class="btn btn-primary" id="submit-btn">
        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
          <path d="M5 12l5 5L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Send for Approval
      </button>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay" style="display: none;">
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Uploading item...</p>
      </div>
    </div>
  </form>

</main>

<!-- JavaScript -->
<script src="/js/app/marketplace/seller-portal-add-items.js"></script>