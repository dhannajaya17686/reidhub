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
    <section class="product-section" aria-labelledby="merchandise-title">
      <div class="section-header">
        <h2 id="merchandise-title" class="section-title">
          All Merchandise
          <span class="section-count"><?php echo isset($merchandiseCount) ? (int)$merchandiseCount : 0; ?></span>
        </h2>

        <!-- Sort Options (unchanged) -->
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
        <?php if (!empty($merchandiseItems)): ?>
          <?php foreach ($merchandiseItems as $item): ?>
            <article class="product-card" tabindex="0" role="article"
                     aria-label="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>, Rs.<?php echo number_format($item['price'], 0, '.', ','); ?>, <?php echo $item['condition'] === 'brand_new' ? 'Brand New' : 'Used'; ?>"
                     data-price="<?php echo (int)$item['price']; ?>"
                     data-type="<?php echo htmlspecialchars(strtolower($item['product_type'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
              <div class="product-card__image">
                <img src="<?php echo htmlspecialchars($item['image'] ?: '/images/placeholders/product.png', ENT_QUOTES, 'UTF-8'); ?>"
                     alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="stock-badge <?php echo $item['stock_badge_class']; ?>">
                  <?php echo htmlspecialchars($item['stock_badge_text'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
              </div>
              <div class="product-card__content">
                <?php if (!empty($item['product_type'])): ?>
                  <div class="product-card__category"><?php echo htmlspecialchars(ucfirst($item['product_type']), ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <h3 class="product-card__title"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="product-card__price">Rs.<?php echo number_format($item['price'], 0, '.', ','); ?></div>
                <div class="product-card__condition">
                  Condition:
                  <span class="condition-badge <?php echo ($item['condition'] === 'brand_new') ? 'condition-badge--new' : 'condition-badge--used'; ?>">
                    <?php echo ($item['condition'] === 'brand_new') ? 'Brand New' : 'Used'; ?>
                  </span>
                </div>
                <div class="product-card__actions" style="display: flex; gap: 8px;">
                  <a href="/dashboard/marketplace/show-product?id=<?php echo (int)$item['id']; ?>" class="btn btn--primary btn--full-width">
                    View Product
                  </a>
                  <button class="btn btn--secondary btn--add-to-cart"
                          title="Add to Cart" data-product-action="add-to-cart"
                          data-product-id="<?php echo (int)$item['id']; ?>"
                          <?php echo ($item['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
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
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fallback: existing sample cards remain if no data -->
          <!-- ...existing static merchandise cards... -->
        <?php endif; ?>
      </div>
    </section>
  </div>

  <!-- Second Hand Items Tab Content -->
  <div class="tab-content is-hidden" data-tab-content="second-hand" id="tab-content-second-hand" role="tabpanel" aria-labelledby="second-hand-tab">
    <section class="product-section" aria-labelledby="secondhand-title">
      <div class="section-header">
        <h2 id="secondhand-title" class="section-title">
          All Second Hand Items
          <span class="section-count"><?php echo isset($secondHandCount) ? (int)$secondHandCount : 0; ?></span>
        </h2>

        <!-- Sort Options (unchanged) -->
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
        <?php if (!empty($secondHandItems)): ?>
          <?php foreach ($secondHandItems as $item): ?>
            <article class="product-card" tabindex="0" role="article"
                     aria-label="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>, Rs.<?php echo number_format($item['price'], 0, '.', ','); ?>, Used"
                     data-price="<?php echo (int)$item['price']; ?>"
                     data-type="<?php echo htmlspecialchars(strtolower($item['product_type'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
              <div class="product-card__image">
                <img src="<?php echo htmlspecialchars($item['image'] ?: '/images/placeholders/product.png', ENT_QUOTES, 'UTF-8'); ?>"
                     alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="stock-badge <?php echo $item['stock_badge_class']; ?>">
                  <?php echo htmlspecialchars($item['stock_badge_text'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
              </div>
              <div class="product-card__content">
                <?php if (!empty($item['product_type'])): ?>
                  <div class="product-card__category"><?php echo htmlspecialchars(ucfirst($item['product_type']), ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <h3 class="product-card__title"><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="product-card__price">Rs.<?php echo number_format($item['price'], 0, '.', ','); ?></div>
                <div class="product-card__condition">
                  Condition:
                  <span class="condition-badge condition-badge--used">Used</span>
                </div>
                <div class="product-card__actions" style="display: flex; gap: 8px;">
                  <a href="/dashboard/marketplace/show-product?id=<?php echo (int)$item['id']; ?>" class="btn btn--primary btn--full-width">
                    View Product
                  </a>
                  <button class="btn btn--secondary btn--add-to-cart"
                          title="Add to Cart" data-product-action="add-to-cart"
                          data-product-id="<?php echo (int)$item['id']; ?>"
                          <?php echo ($item['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
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
          <?php endforeach; ?>
        <?php else: ?>
          <!-- Fallback: existing static second-hand cards remain if no data -->
          <!-- ...existing static second-hand cards... -->
        <?php endif; ?>
      </div>
    </section>
  </div>

</main>

<!-- JavaScript -->
<script type="module" src="/js/app/marketplace/marketplace-user-dashboard.js"></script>
</body>
</html>