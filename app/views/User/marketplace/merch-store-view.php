<link rel="stylesheet" href="/css/app/user/marketplace/merch-store.css">
<!-- Main Dashboard Content Area -->
<main class="dashboard-main" role="main" aria-label="Marketplace Dashboard">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">Merch Store</h1>
    <p class="page-subtitle">
      Buy the merch and second hand items from UCSC community
    </p>

    <!-- Statistics Cards -->
    <div class="page-stats">
      <div class="stat-card">
        <div class="stat-number">247</div>
        <div class="stat-label">Recent Purchases</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">189</div>
        <div class="stat-label">Active Orders</div>
      </div>
      <div class="stat-card">
        <div class="stat-number">12</div>
        <div class="stat-label">My Cart</div>
      </div>
    </div>
  </div>

  <!-- Tab Navigation -->
  <nav class="tab-navigation" aria-label="Product categories">
    <div class="tab-list" role="tablist">
      <button class="tab-button tab-button--active" 
              data-tab="merchandise" 
              role="tab" 
              aria-selected="true" 
              aria-controls="tab-content-merchandise"
              tabindex="0">
        Merchandise
      </button>
      <button class="tab-button" 
              data-tab="second-hand" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-second-hand"
              tabindex="-1">
        Second Hand Items
      </button>
    </div>
  </nav>

  <!-- Merchandise Tab Content -->
  <div class="tab-content" data-tab-content="merchandise" id="tab-content-merchandise" role="tabpanel" aria-labelledby="merchandise-tab">
    
    <!-- All Merchandise Section -->
    <section class="product-section" aria-labelledby="merchandise-title">
      <div class="section-header">
        <h2 id="merchandise-title" class="section-title">
          All Merchandise
          <span class="section-count">2</span>
        </h2>
        
        <!-- Sort Options -->
        <div class="sort-controls">
          <div class="sort-group">
            <label for="sort-price" class="sort-label">Sort by Price:</label>
            <select id="sort-price" class="sort-dropdown" data-sort="price">
              <option value="">Select</option>
              <option value="low-to-high">Low to High</option>
              <option value="high-to-low">High to Low</option>
            </select>
          </div>
          
          <div class="sort-group">
            <label for="sort-type" class="sort-label">Sort by Type:</label>
            <select id="sort-type" class="sort-dropdown" data-sort="type">
              <option value="">All Types</option>
              <option value="clothing">Clothing</option>
              <option value="accessories">Accessories</option>
              <option value="stationery">Stationery</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="product-grid">
        <article class="product-card" tabindex="0" role="article" aria-label="UCSC Tshirt, Rs.2000, Brand New" data-price="2000" data-type="clothing">
          <div class="product-card__image">
            <img src="https://via.placeholder.com/280x280/1e3a8a/ffffff?text=UCSC+Tshirt" alt="UCSC Tshirt - Navy blue polo shirt with white accents">
            <div class="stock-badge stock-badge--in-stock">In Stock</div>
          </div>
          <div class="product-card__content">
            <h3 class="product-card__title">UCSC Tshirt</h3>
            <div class="product-card__price">Rs.2000</div>
            <div class="product-card__condition">
              Condition: <span class="condition-badge condition-badge--new">Brand New</span>
            </div>
            <div class="product-card__actions" style="display: flex; gap: 8px;">
              <a href="/dashboard/marketplace/show-product" class="btn btn--primary btn--full-width">
                View Product
              </a>
              <button class="btn btn--secondary btn--add-to-cart" title="Add to Cart" data-product-action="add-to-cart" data-product-id="1">
                <span class="cart-icon" aria-hidden="true" style="display:inline-flex;align-items:center;">
                  <!-- Simple cart SVG icon -->
                  <svg width="20" height="20" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M6.5 17a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM3.5 4h1.11l1.31 7.39a2 2 0 0 0 2 1.61h5.36a2 2 0 0 0 2-1.61l1.13-5.39H5.12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
                <span class="visually-hidden">Add to Cart</span>
              </button>
            </div>
          </div>
        </article>

        <article class="product-card" tabindex="0" role="article" aria-label="UCSC Hoodie, Rs.3500, Brand New" data-price="3500" data-type="clothing">
          <div class="product-card__image">
            <img src="https://via.placeholder.com/280x280/2563eb/ffffff?text=UCSC+Hoodie" alt="UCSC Hoodie - Navy blue hoodie with university logo">
            <div class="stock-badge stock-badge--low-stock">Low Stock</div>
          </div>
          <div class="product-card__content">
            <div class="product-card__category">Apparel • Official Merchandise</div>
            <h3 class="product-card__title">UCSC Hoodie</h3>
            <div class="product-card__price">Rs.3500</div>
            <div class="product-card__condition">
              Condition: <span class="condition-badge condition-badge--new">Brand New</span>
            </div>
            <div class="product-card__actions" style="display: flex; gap: 8px;">
              <a href="/marketplace/product/2" class="btn btn--primary btn--full-width">
                View Product
              </a>
              <button class="btn btn--secondary btn--add-to-cart" title="Add to Cart" data-product-action="add-to-cart" data-product-id="2">
                <span class="cart-icon" aria-hidden="true" style="display:inline-flex;align-items:center;">
                  <svg width="20" height="20" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M6.5 17a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM3.5 4h1.11l1.31 7.39a2 2 0 0 0 2 1.61h5.36a2 2 0 0 0 2-1.61l1.13-5.39H5.12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
                <span class="visually-hidden">Add to Cart</span>
              </button>
            </div>
          </div>
        </article>
      </div>
    </section>
  </div>

  <!-- Second Hand Items Tab Content -->
  <div class="tab-content is-hidden" data-tab-content="second-hand" id="tab-content-second-hand" role="tabpanel" aria-labelledby="second-hand-tab">
    
    <!-- All Second Hand Items Section -->
    <section class="product-section" aria-labelledby="secondhand-title">
      <div class="section-header">
        <h2 id="secondhand-title" class="section-title">
          All Second Hand Items
          <span class="section-count">2</span>
        </h2>
        
        <!-- Sort Options -->
        <div class="sort-controls">
          <div class="sort-group">
            <label for="sort-price-secondhand" class="sort-label">Sort by Price:</label>
            <select id="sort-price-secondhand" class="sort-dropdown" data-sort="price">
              <option value="">Select</option>
              <option value="low-to-high">Low to High</option>
              <option value="high-to-low">High to Low</option>
            </select>
          </div>
          
          <div class="sort-group">
            <label for="sort-type-secondhand" class="sort-label">Sort by Type:</label>
            <select id="sort-type-secondhand" class="sort-dropdown" data-sort="type">
              <option value="">All Types</option>
              <option value="books">Books</option>
              <option value="electronics">Electronics</option>
              <option value="clothing">Clothing</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="product-grid">
        <article class="product-card" tabindex="0" role="article" aria-label="Programming Book, Rs.650, Used" data-price="650" data-type="books">
          <div class="product-card__image">
            <img src="https://via.placeholder.com/280x280/1e3a8a/ffffff?text=Programming+Book" alt="Programming Book - Blue textbook">
            <div class="stock-badge stock-badge--in-stock">Available</div>
          </div>
          <div class="product-card__content">
            <h3 class="product-card__title">Programming Book</h3>
            <div class="product-card__price">Rs.650</div>
            <div class="product-card__condition">
              Condition: <span class="condition-badge condition-badge--used">Used</span>
            </div>
            <div class="product-card__actions" style="display: flex; gap: 8px;">
              <a href="/marketplace/product/3" class="btn btn--primary btn--full-width">
                View Product
              </a>
              <button class="btn btn--secondary btn--add-to-cart" title="Add to Cart" data-product-action="add-to-cart" data-product-id="3">
                <span class="cart-icon" aria-hidden="true" style="display:inline-flex;align-items:center;">
                  <svg width="20" height="20" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M6.5 17a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM3.5 4h1.11l1.31 7.39a2 2 0 0 0 2 1.61h5.36a2 2 0 0 0 2-1.61l1.13-5.39H5.12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
                <span class="visually-hidden">Add to Cart</span>
              </button>
            </div>
          </div>
        </article>

        <article class="product-card" tabindex="0" role="article" aria-label="Gaming Mouse, Rs.1200, Used" data-price="1200" data-type="electronics">
          <div class="product-card__image">
            <img src="https://via.placeholder.com/280x280/374151/ffffff?text=Gaming+Mouse" alt="Gaming Mouse - Black ergonomic gaming mouse">
            <div class="stock-badge stock-badge--out-of-stock">Sold</div>
          </div>
          <div class="product-card__content">
            <h3 class="product-card__title">Gaming Mouse</h3>
            <div class="product-card__price">Rs.1200</div>
            <div class="product-card__condition">
              Condition: <span class="condition-badge condition-badge--used">Used</span>
            </div>
            <div class="product-card__actions" style="display: flex; gap: 8px;">
              <a href="/marketplace/product/4" class="btn btn--primary btn--full-width">
                View Product
              </a>
              <button class="btn btn--secondary btn--add-to-cart" title="Add to Cart" data-product-action="add-to-cart" data-product-id="4" disabled>
                <span class="cart-icon" aria-hidden="true" style="display:inline-flex;align-items:center;">
                  <svg width="20" height="20" fill="none" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M6.5 17a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0ZM3.5 4h1.11l1.31 7.39a2 2 0 0 0 2 1.61h5.36a2 2 0 0 0 2-1.61l1.13-5.39H5.12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
                <span class="visually-hidden">Add to Cart</span>
              </button>
            </div>
          </div>
        </article>
      </div>
    </section>
  </div>

</main>

<!-- JavaScript -->
<script type="module" src="/js/app/marketplace/marketplace-user-dashboard.js"></script>
</body>
</html>