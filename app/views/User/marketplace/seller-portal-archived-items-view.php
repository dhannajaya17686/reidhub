<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-active-items.css">
<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-archived-items.css">

<!-- Main Archived Items Page -->
<main class="active-items-main" role="main" aria-label="Archived Items Management">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Archived Items</h1>
      <p class="page-subtitle">Manage your archived items and restore them when needed.</p>
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
            <span class="item-status archived">Archived</span>
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
            <button class="btn btn-success btn-sm" onclick="unarchiveItem(1)">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                <polyline points="17,1 21,5 17,9" stroke="currentColor" stroke-width="2"/>
                <path d="M3 11V9a4 4 0 0 1 4-4h14" stroke="currentColor" stroke-width="2"/>
                <polyline points="7,23 3,19 7,15" stroke="currentColor" stroke-width="2"/>
                <path d="M21 13v2a4 4 0 0 1-4 4H3" stroke="currentColor" stroke-width="2"/>
              </svg>
              Unarchive Item
            </button>
          </div>
        </div>
        
        <!-- Item Image -->
        <div class="item-image">
          <img src="/images/marketplace/items/ucsc-tshirt.jpg">
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
            <span class="item-status archived">Archived</span>
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
            <button class="btn btn-success btn-sm" onclick="unarchiveItem(2)">
              <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                <polyline points="17,1 21,5 17,9" stroke="currentColor" stroke-width="2"/>
                <path d="M3 11V9a4 4 0 0 1 4-4h14" stroke="currentColor" stroke-width="2"/>
                <polyline points="7,23 3,19 7,15" stroke="currentColor" stroke-width="2"/>
                <path d="M21 13v2a4 4 0 0 1-4 4H3" stroke="currentColor" stroke-width="2"/>
              </svg>
              Unarchive Item
            </button>
          </div>
        </div>
        
        <!-- Item Image -->
        <div class="item-image">
          <img src="/images/marketplace/items/ucsc-band.jpg" >
        </div>
      </div>
    </div>

  </div>

  <!-- Empty State (hidden by default) -->
  <div class="empty-state" id="empty-state" style="display: none;">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24" fill="none">
        <polyline points="21,8 21,21 3,21 3,8" stroke="currentColor" stroke-width="2"/>
        <rect x="1" y="3" width="22" height="5" stroke="currentColor" stroke-width="2"/>
        <line x1="10" y1="12" x2="14" y2="12" stroke="currentColor" stroke-width="2"/>
      </svg>
    </div>
    <h3 class="empty-title">No Archived Items</h3>
    <p class="empty-description">You don't have any archived items yet. Items you archive will appear here.</p>
    <a href="/marketplace/seller/items" class="btn btn-primary">
      <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2"/>
      </svg>
      Back to Active Items
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
<script src="/js/app/marketplace/seller-portal-archived-items.js"></script>