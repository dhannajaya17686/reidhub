<link rel="stylesheet" href="/css/app/user/marketplace/specific-product.css">

<!-- Main Content Area -->
<main class="product-main" role="main" aria-label="Product Details">
  
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/marketplace" class="breadcrumb__link">Marketplace</a>
      </li>
      <li class="breadcrumb__item">
        <a href="/marketplace/merchandise" class="breadcrumb__link">Merchandise</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        UCSC T-Shirt
      </li>
    </ol>
  </nav>

  <!-- Product Container -->
  <div class="product-container">
    
    <!-- Product Images Section -->
    <section class="product-images" aria-label="Product Images">
      <div class="image-gallery">
        <div class="main-image">
          <img src="https://via.placeholder.com/500x500/1e3a8a/ffffff?text=UCSC+T-Shirt" 
               alt="UCSC T-Shirt - Navy blue polo shirt with white accents" 
               class="main-image__img">
          <div class="stock-badge stock-badge--in-stock">In Stock</div>
        </div>
        
        <div class="image-thumbnails">
          <button class="thumbnail thumbnail--active" data-image="0">
            <img src="https://via.placeholder.com/100x100/1e3a8a/ffffff?text=Front" alt="Front view">
          </button>
          <button class="thumbnail" data-image="1">
            <img src="https://via.placeholder.com/100x100/1e3a8a/ffffff?text=Back" alt="Back view">
          </button>
          <button class="thumbnail" data-image="2">
            <img src="https://via.placeholder.com/100x100/1e3a8a/ffffff?text=Side" alt="Side view">
          </button>
        </div>
      </div>
    </section>

    <!-- Product Information Section -->
    <section class="product-info" aria-label="Product Information">
      <div class="product-header">
        <div class="product-category">Apparel • Official Merchandise</div>
        <h1 class="product-title">UCSC T-Shirt</h1>
        <div class="product-price">Rs. 2,000</div>
      </div>

      <div class="product-details">
        <div class="detail-item">
          <span class="detail-label">Condition:</span>
          <span class="condition-badge condition-badge--new">Brand New</span>
        </div>
        
        <div class="detail-item">
          <span class="detail-label">Category:</span>
          <span class="detail-value">Clothing</span>
        </div>
        
        <div class="detail-item">
          <span class="detail-label">Availability:</span>
          <span class="availability availability--in-stock">In Stock (15 available)</span>
        </div>
      </div>

      <div class="product-description">
        <h3 class="description-title">Description</h3>
        <p class="description-text">
          University of Colombo School of Computing printed shirt. 
          Made from baby pique cotton material, dark blue, front with curved 
          hemline, side seam detail hemline and band acoustic. Premium quality.
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
            <div class="seller-name">Students Union of UCSC</div>
            <div class="seller-location">Narahena Mahason</div>
          </div>
        </div>
      </div>

      <!-- Purchase Section -->
      <div class="purchase-section">
        <div class="quantity-selector">
          <label for="quantity" class="quantity-label">Quantity:</label>
          <div class="quantity-controls">
            <button type="button" class="quantity-btn quantity-btn--minus" aria-label="Decrease quantity">-</button>
            <input type="number" id="quantity" name="quantity" min="1" max="15" value="1" class="quantity-input">
            <button type="button" class="quantity-btn quantity-btn--plus" aria-label="Increase quantity">+</button>
          </div>
        </div>

        <div class="purchase-actions">
          <button class="btn btn--primary btn--large btn--full-width">
            <span class="btn-icon">
              <svg width="20" height="20" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                <path d="M6.5 17a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM3.5 4h1.11l1.31 7.39a2 2 0 0 0 2 1.61h5.36a2 2 0 0 0 2-1.61l1.13-5.39H5.12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            Add to Cart
          </button>
          
          <div class="secondary-actions">
            
            <button class="btn btn--secondary btn--outline btn--report" data-product-id="1">
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