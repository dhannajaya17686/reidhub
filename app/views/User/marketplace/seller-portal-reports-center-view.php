<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-orders.css">

<main class="orders-main" role="main" aria-label="Seller Reports Center">
  <section id="seller-moderation-status" data-moderation-context="reports-center"></section>

  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Reports Center</h1>
      <p class="page-subtitle">Review reported items, check violation details, and contact moderators per report.</p>
    </div>
  </div>

  <div class="order-tabs">
    <button class="tab-btn active" data-status="all">All Reports</button>
    <button class="tab-btn" data-status="pending">Pending</button>
    <button class="tab-btn" data-status="under-review">Under Review</button>
    <button class="tab-btn" data-status="resolved">Resolved</button>
    <button class="tab-btn" data-status="archived">Archived</button>
  </div>

  <div class="search-filters">
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none">
        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65" stroke="currentColor" stroke-width="2"/>
      </svg>
      <input type="text" id="search-input" placeholder="Search reports by product, reporter, or reason">
    </div>

    <div class="filters">
      <select id="category-filter" class="filter-select">
        <option value="">All Categories</option>
        <option value="inappropriate">Inappropriate Content</option>
        <option value="spam">Spam</option>
        <option value="fraud">Fraud/Scam</option>
        <option value="copyright">Copyright Violation</option>
        <option value="other">Other</option>
      </select>
    </div>
  </div>

  <div class="orders-table-container">
    <table class="orders-table">
      <thead>
        <tr>
          <th>Report ID</th>
          <th>Item</th>
          <th>Violation Details</th>
          <th>Reporter</th>
          <th>Status</th>
          <th>Admin Communication</th>
        </tr>
      </thead>
      <tbody id="reports-tbody">
        <tr>
          <td colspan="6" class="empty-description">Loading reports...</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="empty-state" id="empty-state" style="display:none;">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="2"/>
      </svg>
    </div>
    <h3 class="empty-title">No Reports Found</h3>
    <p class="empty-description">No reports match your current filters.</p>
  </div>
</main>

<script src="/js/app/marketplace/seller-moderation-status.js"></script>
<script src="/js/app/marketplace/seller-portal-reports-center.js"></script>
