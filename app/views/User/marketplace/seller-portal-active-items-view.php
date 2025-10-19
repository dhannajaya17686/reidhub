<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-active-items.css">

<!-- Main Active Items Page -->
<main class="active-items-main" role="main" aria-label="Active Items Management">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Active Items</h1>
      <p class="page-subtitle">Manage your active items and track their progress.</p>
    </div>
  </div>

  <!-- Items Grid -->
  <div class="items-grid" id="items-grid">
    
    <!-- Item 1: UCSC TShirt -->
    <div class="item-card" data-item-id="1">
      <div class="item-content">
        <div class="item-info">
          <h3 class="item-title">UCSC Tshirt</h3>
          <div class="item-price">Rs.2000</div>
          <div class="item-meta">
            <span class="item-condition">Condition: Brand New</span>
          </div>
          
          <!-- Item Actions -->
          <div class="item-actions">
            <button class="btn btn-primary btn-sm" onclick="viewItem(1)">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
              </svg>
              View
            </button>
            <button class="btn btn-primary btn-sm" onclick="editItem(1)">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2"/>
                <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2"/>
              </svg>
              Edit
            </button>
            <button class="btn btn-secondary btn-sm" onclick="archiveItem(1)">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                <polyline points="21,8 21,21 3,21 3,8" stroke="currentColor" stroke-width="2"/>
                <rect x="1" y="3" width="22" height="5" stroke="currentColor" stroke-width="2"/>
                <line x1="10" y1="12" x2="14" y2="12" stroke="currentColor" stroke-width="2"/>
              </svg>
              Add to Archive
            </button>
          </div>
        </div>
        
        <!-- Item Image -->
        <div class="item-image">
          <img src="/images/marketplace/items/ucsc-tshirt.jpg" alt="UCSC Tshirt">
        </div>
      </div>
    </div>

    <!-- Item 2: UCSC Band -->
    <div class="item-card" data-item-id="2">
      <div class="item-content">
        <div class="item-info">
          <h3 class="item-title">UCSC Band</h3>
          <div class="item-price">Rs.300</div>
          <div class="item-meta">
            <span class="item-condition">Condition: Brand New</span>
          </div>
          
          <!-- Item Actions -->
          <div class="item-actions">
            <button class="btn btn-primary btn-sm" onclick="viewItem(2)">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
              </svg>
              View
            </button>
            <button class="btn btn-primary btn-sm" onclick="editItem(2)">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2"/>
                <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2"/>
              </svg>
              Edit
            </button>
            <button class="btn btn-secondary btn-sm" onclick="archiveItem(2)">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                <polyline points="21,8 21,21 3,21 3,8" stroke="currentColor" stroke-width="2"/>
                <rect x="1" y="3" width="22" height="5" stroke="currentColor" stroke-width="2"/>
                <line x1="10" y1="12" x2="14" y2="12" stroke="currentColor" stroke-width="2"/>
              </svg>
              Add to Archive
            </button>
          </div>
        </div>
        
        <!-- Item Image -->
        <div class="item-image">
          <img src="/images/marketplace/items/ucsc-band.jpg" alt="UCSC Band">
        </div>
      </div>
    </div>

  </div>

  <!-- Empty State (hidden by default) -->
  <div class="empty-state" id="empty-state" style="display: none;">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24" fill="none">
        <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
        <circle cx="9" cy="9" r="2" stroke="currentColor" stroke-width="2"/>
        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" stroke="currentColor" stroke-width="2"/>
      </svg>
    </div>
    <h3 class="empty-title">No Active Items</h3>
    <p class="empty-description">You don't have any active items yet. Add your first item to get started!</p>
    <a href="/marketplace/seller/add-item" class="btn btn-primary">
      <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
        <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2"/>
        <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"/>
      </svg>
      Add Your First Item
    </a>
  </div>

</main>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay" style="display: none;">
  <div class="loading-spinner">
    <div class="spinner"></div>
    <p>Processing...</p>
  </div>
</div>

<!-- JavaScript -->
<script src="/js/app/marketplace/seller-portal-active-items.js"></script>