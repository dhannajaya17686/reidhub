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

  <!-- Items Grid (now empty; will be filled by JS) -->
  <div class="items-grid" id="items-grid"></div>

  <!-- Empty State -->
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
    <a href="/dashboard/marketplace/seller/add" class="btn btn-primary">
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