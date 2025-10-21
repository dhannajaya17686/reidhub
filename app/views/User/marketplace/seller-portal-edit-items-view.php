<?php
// Expect $item (may be null). Prepare helpers.
$itm = $item ?? null;
$imgs = $itm && !empty($itm['images']) ? json_decode($itm['images'], true) : [];
if (!is_array($imgs)) $imgs = [];
$pm = $itm && !empty($itm['payment_methods']) ? json_decode($itm['payment_methods'], true) : [];
if (!is_array($pm)) $pm = [];
$codChecked = in_array('cash_on_delivery', $pm, true);
$preChecked = in_array('preorder', $pm, true);
?>
<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-add-items.css">
<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-edit-items.css">

<!-- Main Edit Items Form -->
<main class="add-items-main" role="main" aria-label="Edit Item Form">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Edit Item</h1>
      <p class="page-subtitle">Update your item details and save changes.</p>
    </div>
  </div>

  <!-- Edit Item Form -->
  <form class="add-item-form" id="edit-item-form" method="POST" action="/dashboard/marketplace/seller/edit" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($itm['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

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
            value="<?php echo htmlspecialchars($itm['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
            maxlength="100"
          >
          <div class="form-error" id="item-name-error"></div>
        </div>

        <!-- Category (product_type) -->
        <div class="form-group">
          <label for="category" class="form-label">Category *</label>
          <select id="category" name="category" class="form-select" required>
            <?php
              $types = ['apparel','accessories','stationery','electronics','books','other'];
              $selType = strtolower((string)($itm['product_type'] ?? ''));
              echo '<option value="">Select Category</option>';
              foreach ($types as $t) {
                $sel = ($selType === $t) ? 'selected' : '';
                echo '<option value="'.$t.'" '.$sel.'>'.ucfirst($t).'</option>';
              }
            ?>
          </select>
          <div class="form-error" id="category-error"></div>
        </div>
      </div>

      <!-- Condition -->
      <div class="form-group">
        <label class="form-label">Condition *</label>
        <div class="radio-group">
          <?php $cond = $itm['condition_type'] ?? ''; ?>
          <label class="radio-option">
            <input type="radio" name="condition" value="brand_new" <?php echo ($cond === 'brand_new' ? 'checked' : ''); ?> required>
            <span class="radio-custom"></span>
            <span class="radio-text">Brand New</span>
          </label>
          <label class="radio-option">
            <input type="radio" name="condition" value="used" <?php echo ($cond === 'used' ? 'checked' : ''); ?> required>
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
        ><?php echo htmlspecialchars($itm['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        <div class="char-counter">
          <span id="desc-count"><?php echo strlen((string)($itm['description'] ?? '')); ?></span>/500 characters
        </div>
        <div class="form-error" id="description-error"></div>
      </div>
    </div>

    <!-- Pricing & Inventory Section -->
    <div class="form-section">
      <h2 class="section-title">Pricing & Inventory</h2>
      <div class="form-grid">
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
              value="<?php echo htmlspecialchars((string)($itm['price'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
              min="1" max="999999" step="1" required
            >
          </div>
          <div class="form-error" id="item-price-error"></div>
        </div>

        <div class="form-group">
          <label for="item-quantity" class="form-label">Item Quantity *</label>
          <div class="quantity-input">
            <button type="button" class="qty-btn qty-decrease" data-target="item-quantity">-</button>
            <input 
              type="number" 
              id="item-quantity" 
              name="item_quantity" 
              class="form-input qty-field" 
              value="<?php echo (int)($itm['stock_quantity'] ?? 1); ?>"
              min="1" max="999" required
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
      
      <div class="form-group">
        <label class="form-label">Accepted Payment Methods *</label>
        <div class="checkbox-group">
          <label class="checkbox-option">
            <input type="checkbox" name="payment_methods[]" value="cash_on_delivery" id="cod-option" <?php echo $codChecked ? 'checked' : ''; ?>>
            <span class="checkbox-custom"></span>
            <div class="checkbox-content">
              <span class="checkbox-title">Cash on Delivery</span>
              <span class="checkbox-description">Customer pays when they receive the item</span>
            </div>
          </label>
          
          <label class="checkbox-option">
            <input type="checkbox" name="payment_methods[]" value="preorder" id="preorder-option" <?php echo $preChecked ? 'checked' : ''; ?>>
            <span class="checkbox-custom"></span>
            <div class="checkbox-content">
              <span class="checkbox-title">Pre-order / Upfront Payment</span>
              <span class="checkbox-description">Customer pays before receiving the item</span>
            </div>
          </label>
        </div>
        <div class="form-error" id="payment-methods-error"></div>
      </div>

      <!-- Bank Account Details -->
      <div class="bank-details-section" id="bank-details-section" style="<?php echo $preChecked ? '' : 'display:none;'; ?>">
        <h3 class="subsection-title">Bank Account Details</h3>
        <p class="subsection-subtitle">Provide your bank account details for receiving payments.</p>
        
        <div class="form-grid">
          <div class="form-group">
            <label for="bank-name" class="form-label">Bank Name *</label>
            <?php $bank = $itm['bank_name'] ?? ''; ?>
            <select id="bank-name" name="bank_name" class="form-select" <?php echo $preChecked ? 'required' : ''; ?>>
              <?php
                $banks = [
                  'commercial_bank' => 'Commercial Bank of Ceylon',
                  'peoples_bank' => "People's Bank",
                  'bank_of_ceylon' => 'Bank of Ceylon',
                  'hatton_national' => 'Hatton National Bank',
                  'sampath_bank' => 'Sampath Bank',
                  'seylan_bank' => 'Seylan Bank',
                  'dfcc_bank' => 'DFCC Bank',
                  'ndb_bank' => 'National Development Bank',
                  'nations_trust' => 'Nations Trust Bank',
                  'union_bank' => 'Union Bank',
                  'other' => 'Other'
                ];
                echo '<option value="">Select Bank</option>';
                foreach ($banks as $val => $label) {
                  $sel = ($bank === $val) ? 'selected' : '';
                  echo "<option value=\"{$val}\" {$sel}>".htmlspecialchars($label, ENT_QUOTES, 'UTF-8')."</option>";
                }
              ?>
            </select>
            <div class="form-error" id="bank-name-error"></div>
          </div>

          <div class="form-group">
            <label for="bank-branch" class="form-label">Branch *</label>
            <input type="text" id="bank-branch" name="bank_branch" class="form-input"
                   placeholder="e.g., Nugegoda" value="<?php echo htmlspecialchars($itm['bank_branch'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   <?php echo $preChecked ? 'required' : ''; ?>>
            <div class="form-error" id="bank-branch-error"></div>
          </div>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="account-name" class="form-label">Account Holder Name *</label>
            <input type="text" id="account-name" name="account_name" class="form-input"
                   placeholder="e.g., John Doe" value="<?php echo htmlspecialchars($itm['account_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   <?php echo $preChecked ? 'required' : ''; ?>>
            <div class="form-error" id="account-name-error"></div>
          </div>

          <div class="form-group">
            <label for="account-number" class="form-label">Account Number *</label>
            <input type="text" id="account-number" name="account_number" class="form-input"
                   placeholder="e.g., 8001234567890"
                   value="<?php echo htmlspecialchars($itm['account_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   pattern="[0-9]{10,18}" title="Please enter a valid account number (10-18 digits)"
                   <?php echo $preChecked ? 'required' : ''; ?>>
            <div class="form-error" id="account-number-error"></div>
          </div>
        </div>

        <!-- Bank Account Preview -->
        <div class="bank-preview" id="bank-preview" style="<?php echo ($itm['bank_name']||$itm['bank_branch']||$itm['account_name']||$itm['account_number']) ? '' : 'display:none;'; ?>">
          <h4 class="preview-title">Bank Account Preview</h4>
          <div class="preview-details">
            <div class="preview-item">
              <span class="preview-label">Bank:</span>
              <span class="preview-value" id="preview-bank"><?php echo htmlspecialchars($banks[$bank] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="preview-item">
              <span class="preview-label">Branch:</span>
              <span class="preview-value" id="preview-branch"><?php echo htmlspecialchars($itm['bank_branch'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="preview-item">
              <span class="preview-label">Account Name:</span>
              <span class="preview-value" id="preview-account-name"><?php echo htmlspecialchars($itm['account_name'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="preview-item">
              <span class="preview-label">Account Number:</span>
              <span class="preview-value" id="preview-account-number"><?php echo htmlspecialchars($itm['account_number'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Images Section -->
    <?php
      $img0 = $imgs[0] ?? null;
      $img1 = $imgs[1] ?? null;
      $img2 = $imgs[2] ?? null;
      $img3 = $imgs[3] ?? null;
    ?>
    <div class="form-section">
      <h2 class="section-title">Update Images</h2>
      <p class="section-subtitle">Upload up to 4 images. The first image will be the main image.</p>
      
      <div class="image-upload-container">
        <!-- Main Image -->
        <div class="image-slot main-image" data-slot="0">
          <input type="file" id="image-0" name="images[]" accept="image/*" class="image-input" hidden>
          <input type="hidden" name="image_slot[]" value="0">
          <div class="image-preview" id="preview-0">
            <?php if ($img0): ?>
              <img src="<?php echo htmlspecialchars($img0, ENT_QUOTES, 'UTF-8'); ?>" alt="Main Image" onerror="this.src='/images/placeholders/product.png'">
            <?php else: ?>
              <div class="upload-placeholder">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                  <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="upload-text">Main Image</span>
                <span class="upload-hint">Click to upload</span>
              </div>
            <?php endif; ?>
          </div>
          <button type="button" class="remove-image" <?php echo $img0 ? '' : 'style="display: none;"'; ?>>×</button>
          <div class="main-badge">MAIN</div>
        </div>

        <div class="image-slot" data-slot="1">
          <input type="file" id="image-1" name="images[]" accept="image/*" class="image-input" hidden>
          <input type="hidden" name="image_slot[]" value="1">
          <div class="image-preview" id="preview-1">
            <?php if ($img1): ?>
              <img src="<?php echo htmlspecialchars($img1, ENT_QUOTES, 'UTF-8'); ?>" alt="Image 2" onerror="this.src='/images/placeholders/product.png'">
            <?php else: ?>
              <div class="upload-placeholder">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                  <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="upload-text">Image 2</span>
                <span class="upload-hint">Optional</span>
              </div>
            <?php endif; ?>
          </div>
          <button type="button" class="remove-image" <?php echo $img1 ? '' : 'style="display: none;"'; ?>>×</button>
        </div>

        <div class="image-slot" data-slot="2">
          <input type="file" id="image-2" name="images[]" accept="image/*" class="image-input" hidden>
          <input type="hidden" name="image_slot[]" value="2">
          <div class="image-preview" id="preview-2">
            <?php if ($img2): ?>
              <img src="<?php echo htmlspecialchars($img2, ENT_QUOTES, 'UTF-8'); ?>" alt="Image 3" onerror="this.src='/images/placeholders/product.png'">
            <?php else: ?>
              <div class="upload-placeholder">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                  <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="upload-text">Image 3</span>
                <span class="upload-hint">Optional</span>
              </div>
            <?php endif; ?>
          </div>
          <button type="button" class="remove-image" <?php echo $img2 ? '' : 'style="display: none;"'; ?>>×</button>
        </div>

        <div class="image-slot" data-slot="3">
          <input type="file" id="image-3" name="images[]" accept="image/*" class="image-input" hidden>
          <input type="hidden" name="image_slot[]" value="3">
          <div class="image-preview" id="preview-3">
            <?php if ($img3): ?>
              <img src="<?php echo htmlspecialchars($img3, ENT_QUOTES, 'UTF-8'); ?>" alt="Image 4" onerror="this.src='/images/placeholders/product.png'">
            <?php else: ?>
              <div class="upload-placeholder">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2"/>
                  <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2"/>
                  <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span class="upload-text">Image 4</span>
                <span class="upload-hint">Optional</span>
              </div>
            <?php endif; ?>
          </div>
          <button type="button" class="remove-image" <?php echo $img3 ? '' : 'style="display: none;"'; ?>>×</button>
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
        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
          <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2"/>
        </svg>
        Cancel
      </button>
      <button type="submit" class="btn btn-primary" id="submit-btn">
        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
          <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
        </svg>
        Save Changes
      </button>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loading-overlay" style="display: none;">
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Saving changes...</p>
      </div>
    </div>
  </form>

</main>

<!-- JavaScript -->
<script src="/js/app/marketplace/seller-portal-edit-items.js"></script>
