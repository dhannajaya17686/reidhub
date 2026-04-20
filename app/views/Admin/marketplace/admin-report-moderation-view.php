<link rel="stylesheet" href="/css/app/admin/marketplace/reported.css">

<main class="reported-main" role="main" aria-label="Admin Marketplace Seller Moderation">
  <div class="page-header">
    <h1 class="page-title">Seller Moderation</h1>
    <p class="page-subtitle">Choose a seller to review reports, issue warnings, and manage ban status from their profile page.</p>
  </div>

  <div class="report-tabs">
    <button class="tab-btn active" data-state="all">All Sellers</button>
    <button class="tab-btn" data-state="active">Active Sellers</button>
    <button class="tab-btn" data-state="banned">Banned Sellers</button>
  </div>

  <div class="search-filters">
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none">
        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
        <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"/>
      </svg>
      <input type="text" id="search-input" placeholder="Search by seller name or email...">
    </div>
  </div>

  <div class="reports-table-container">
    <table class="reports-table">
      <thead>
        <tr>
          <th>Seller</th>
          <th>Total Reports</th>
          <th>Open Reports</th>
          <th>Warnings</th>
          <th>Account Status</th>
          <th>Last Report</th>
          <th>Seller ID</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="moderation-tbody">
        <tr>
          <td colspan="8" class="empty-description">Loading seller moderation profiles...</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div id="empty-state" class="empty-state" style="display:none;">
    <svg class="empty-icon" viewBox="0 0 24 24" fill="none">
      <path d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <h3 class="empty-title">No sellers found</h3>
    <p class="empty-description">No seller profiles match your current filters.</p>
  </div>
</main>

<script src="/js/app/admin/marketplace/admin-report-moderation.js"></script>
