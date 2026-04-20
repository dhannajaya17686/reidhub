<?php
// filepath: /home/dhananjaya/Documents/projects/reidhub/app/views/User/lost-and-found/lost-and-found-items-view.php
?>
<link rel="stylesheet" href="/css/app/user/lost-and-found/lost-and-found-items.css">

<!-- Main Content Area -->
<main class="lost-found-main" role="main" aria-label="Lost and Found Items">
  <div class="container">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1 class="page-title">Lost & Found</h1>
          <p class="page-subtitle">Help reunite lost items with their owners or find your missing belongings</p>
        </div>
        
        <div class="header-actions">
          <button class="btn btn--secondary" id="report-lost-btn">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.35-4.35"/>
            </svg>
            Report Lost Item
          </button>
          <button class="btn btn--primary" id="report-found-btn">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l11 11Z"/>
            </svg>
            Report Found Item
          </button>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="search-filters-section">
        <div class="search-container">
          <input type="text" id="search-input" class="search-input" placeholder="Search by item name, description, or reporter..." aria-label="Search items">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
        </div>
        
        <div class="filters-row">
          <select id="category-filter" class="filter-select" aria-label="Filter by category">
            <option value="">All Categories</option>
            <option value="electronics">Electronics</option>
            <option value="clothing">Clothing & Accessories</option>
            <option value="bags">Bags & Wallets</option>
            <option value="books">Books & Stationery</option>
            <option value="jewelry">Jewelry</option>
            <option value="keys">Keys & Cards</option>
            <option value="sports">Sports Equipment</option>
            <option value="other">Other</option>
          </select>
          
          <select id="location-filter" class="filter-select" aria-label="Filter by location">
            <option value="">All Locations</option>
            <option value="library">Library</option>
            <option value="cafeteria">Cafeteria</option>
            <option value="classroom">Classroom</option>
            <option value="parking">Parking Area</option>
            <option value="sports-complex">Sports Complex</option>
            <option value="dormitory">Dormitory</option>
            <option value="admin-building">Admin Building</option>
            <option value="other-location">Other</option>
          </select>
          
          <select id="date-filter" class="filter-select" aria-label="Filter by date">
            <option value="">All Time</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
          </select>
          
          <button class="filter-clear-btn" id="clear-filters" title="Clear all filters">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="content-tabs" role="tablist">
      <button class="tab-button tab-button--active" data-tab="all" role="tab" aria-selected="true">
        All Items
        <span class="tab-count" id="count-all">0</span>
      </button>
      <button class="tab-button" data-tab="lost" role="tab" aria-selected="false">
        Lost
        <span class="tab-count" id="count-lost">0</span>
      </button>
      <button class="tab-button" data-tab="found" role="tab" aria-selected="false">
        Found
        <span class="tab-count" id="count-found">0</span>
      </button>
      <button class="tab-button" data-tab="my-reports" role="tab" aria-selected="false">
        My Reports
        <span class="tab-count" id="count-my-reports">0</span>
      </button>
      <button class="tab-button" data-tab="claimed" role="tab" aria-selected="false">
        Resolved
        <span class="tab-count" id="count-claimed">0</span>
      </button>
    </div>

    <!-- Items Grid -->
    <div class="items-section">
      <div class="items-grid" id="items-grid">
        <!-- Loading spinner -->
        <div class="loading-spinner" id="loading-spinner">
          <div class="spinner"></div>
          <p>Loading items...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div class="empty-state" id="empty-state" style="display: none;">
        <div class="empty-icon">📋</div>
        <h3>No items found</h3>
        <p>No lost or found items match your current filters, or none have been reported yet.</p>
        <div class="empty-actions">
          <button class="btn btn--primary" onclick="document.getElementById('report-lost-btn').click()">
            Report Lost Item
          </button>
          <button class="btn btn--secondary" onclick="document.getElementById('report-found-btn').click()">
            Report Found Item
          </button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-container" id="pagination-container">
      <div class="pagination-info">
        Showing <span id="showing-start">1</span>-<span id="showing-end">12</span> of <span id="total-items">0</span> items
      </div>
      <div class="pagination-controls">
        <button class="page-btn page-btn--prev" id="prev-btn" disabled>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15,18 9,12 15,6"/>
          </svg>
          Previous
        </button>
        <button class="page-btn page-btn--next" id="next-btn">
          Next
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</main>

<!-- Item Details Modal -->
<div class="modal" id="item-details-modal" aria-hidden="true" role="dialog" aria-labelledby="modal-title">
  <div class="modal-backdrop"></div>
  <div class="modal-content modal-content--large">
    <div class="modal-header">
      <h2 class="modal-title" id="modal-title">Item Details</h2>
      <button class="modal-close" aria-label="Close modal">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>
    
    <div class="modal-body" id="item-details-content">
      <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Loading details...</p>
      </div>
    </div>
  </div>
</div>

<style>
.loading-spinner {
  grid-column: 1 / -1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.loading-spinner .spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e5e7eb;
  border-top-color: #2563eb;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-spinner p {
  margin-top: 16px;
  color: #6b7280;
  font-size: 14px;
}
</style>

<script>
  // Expose current user ID to JavaScript
  window.currentUserId = <?php echo isset($user['id']) ? (int)$user['id'] : 'null'; ?>;
</script>
<script src="/js/app/lost-and-found/lost-and-found-items.js"></script>