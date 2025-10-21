<?php
// Expect $product (may be null)
$prd = $product ?? null;
?>
<link rel="stylesheet" href="/css/app/user/marketplace/specific-product.css">

<?php if (!$prd): ?>
<script>
  alert('Product not found or unavailable.');
  history.back();
</script>
<?php return; endif; ?>

<!-- Main Content Area -->
<main class="product-main" role="main" aria-label="Product Details">
  
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/dashboard/marketplace/merch-store" class="breadcrumb__link">Marketplace</a>
      </li>
      <li class="breadcrumb__item">
        <a href="/dashboard/marketplace/merch-store" class="breadcrumb__link">
          <?php echo htmlspecialchars($prd['category_label'] ?? 'Merchandise', ENT_QUOTES, 'UTF-8'); ?>
        </a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        <?php echo htmlspecialchars($prd['title'], ENT_QUOTES, 'UTF-8'); ?>
      </li>
    </ol>
  </nav>

  <!-- Product Container -->
  <div class="product-container">
    
    <!-- Product Images Section -->
    <section class="product-images" aria-label="Product Images">
      <div class="image-gallery">
        <div class="main-image">
          <img src="<?php echo htmlspecialchars($prd['main_image'], ENT_QUOTES, 'UTF-8'); ?>" 
               alt="<?php echo htmlspecialchars($prd['title'], ENT_QUOTES, 'UTF-8'); ?>" 
               class="main-image__img"
               onerror="this.src='/images/placeholders/product.png'">
          <div class="stock-badge <?php echo htmlspecialchars($prd['stock_class'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($prd['stock_text'], ENT_QUOTES, 'UTF-8'); ?>
          </div>
        </div>
        
        <div class="image-thumbnails">
          <?php foreach (($prd['images'] ?? []) as $idx => $img): ?>
            <button class="thumbnail <?php echo $idx === 0 ? 'thumbnail--active' : ''; ?>" data-image="<?php echo (int)$idx; ?>">
              <img src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="Image <?php echo (int)$idx + 1; ?>" onerror="this.src='/images/placeholders/product.png'">
            </button>
          <?php endforeach; ?>
          <?php if (empty($prd['images'])): ?>
            <button class="thumbnail thumbnail--active" data-image="0">
              <img src="/images/placeholders/product.png" alt="Image 1">
            </button>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Product Information Section -->
    <section class="product-info" aria-label="Product Information">
      <div class="product-header">
        <div class="product-category">
          <?php
            $pt = trim((string)($prd['product_type_label'] ?? ''));
            $catText = $prd['category'] === 'second-hand' ? 'Second Hand' : 'Official Merchandise';
            echo htmlspecialchars(($pt ? $pt . ' • ' : '') . $catText, ENT_QUOTES, 'UTF-8');
          ?>
        </div>
        <h1 class="product-title"><?php echo htmlspecialchars($prd['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="product-price">Rs. <?php echo number_format($prd['price'], 0, '.', ','); ?></div>
      </div>

      <div class="product-details">
        <div class="detail-item">
          <span class="detail-label">Condition:</span>
          <span class="condition-badge <?php echo htmlspecialchars($prd['condition_class'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($prd['condition_text'], ENT_QUOTES, 'UTF-8'); ?>
          </span>
        </div>
        
        <div class="detail-item">
          <span class="detail-label">Category:</span>
          <span class="detail-value"><?php echo htmlspecialchars($prd['product_type_label'] ?: 'Other', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        
        <div class="detail-item">
          <span class="detail-label">Availability:</span>
          <span class="availability <?php echo htmlspecialchars($prd['availability_class'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php echo htmlspecialchars($prd['availability_text'], ENT_QUOTES, 'UTF-8'); ?>
          </span>
        </div>
      </div>

      <div class="product-description">
        <h3 class="description-title">Description</h3>
        <p class="description-text">
          <?php echo nl2br(htmlspecialchars($prd['description'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
        </p>
      </div>

      <!-- Seller Information -->
      <div class="seller-info">
        <h3 class="seller-title">Seller Information</h3>
        <div class="seller-card">
          <div class="seller-avatar">
            <img src="https://via.placeholder.com/48x48/0466C8/ffffff?text=SU" alt="Seller avatar">
          </div>
          <div class="seller-details">
            <div class="seller-name"><?php echo 'Seller #' . (int)$prd['seller_id']; ?></div>
            <div class="seller-location">UCSC Community</div>
          </div>
        </div>
      </div>

      <!-- Purchase Section -->
      <div class="purchase-section">
        <div class="quantity-selector">
          <label for="quantity" class="quantity-label">Quantity:</label>
          <div class="quantity-controls">
            <button type="button" class="quantity-btn quantity-btn--minus" aria-label="Decrease quantity">-</button>
            <input
              type="number"
              id="quantity"
              name="quantity"
              min="1"
              max="<?php echo max((int)$prd['stock_quantity'], 1); ?>"
              value="1"
              class="quantity-input"
              <?php echo ((int)$prd['stock_quantity'] <= 0) ? 'disabled' : ''; ?>
            >
            <button type="button" class="quantity-btn quantity-btn--plus" aria-label="Increase quantity">+</button>
          </div>
        </div>

        <div class="purchase-actions">
          <button class="btn btn--primary btn--large btn--full-width"
                  data-product-id="<?php echo (int)$prd['id']; ?>"
                  id="add-to-cart-button"
                  <?php echo ((int)$prd['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
            <span class="btn-icon">
              <svg width="20" height="20" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M6.5 17a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM3.5 4h1.11l1.31 7.39a2 2 0 0 0 2 1.61h5.36a2 2 0 0 0 2-1.61l1.13-5.39H5.12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Add to Cart
          </button>
          
          <div class="secondary-actions">
            <button class="btn btn--secondary btn--outline btn--report" data-product-id="<?php echo (int)$prd['id']; ?>">
              <span class="btn-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                  <path d="M10 9a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-1.5 0v-2.5A.75.75 0 0 1 10 9ZM10 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" fill="currentColor"/>
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.719c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
                </svg>
              </span>
              Report Item
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Report Modal -->
  <div class="modal-overlay" id="report-modal" role="dialog" aria-labelledby="report-title" aria-modal="true" style="display: none;">
    <div class="modal">
      <div class="modal-header">
        <h2 id="report-title" class="modal-title">Report This Item</h2>
        <button class="modal-close" aria-label="Close modal">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
      
      <form class="modal-body" id="report-form">
        <div class="form-group">
          <label for="report-reason" class="form-label">Reason for reporting:</label>
          <select id="report-reason" name="reason" class="form-select" required>
            <option value="">Select a reason</option>
            <option value="inappropriate">Inappropriate content</option>
            <option value="counterfeit">Counterfeit or fake item</option>
            <option value="misleading">Misleading description</option>
            <option value="overpriced">Unreasonably overpriced</option>
            <option value="prohibited">Prohibited item</option>
            <option value="other">Other</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="report-details" class="form-label">Additional details (optional):</label>
          <textarea id="report-details" name="details" class="form-textarea" rows="4" placeholder="Please provide more information about your report..."></textarea>
        </div>
        
        <div class="modal-actions">
          <button type="button" class="btn btn--secondary" id="cancel-report">Cancel</button>
          <button type="submit" class="btn btn--primary">Submit Report</button>
        </div>
      </form>
    </div>
  </div>

</main>

<!-- JavaScript -->
<script type="module" src="/js/app/marketplace/specific-product.js"></script>
</body>
</html>