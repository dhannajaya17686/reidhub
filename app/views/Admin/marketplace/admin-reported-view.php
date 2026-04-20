<link rel="stylesheet" href="/css/app/admin/marketplace/reported.css">

<!-- Main Reported Items Dashboard -->
<main class="reported-main" role="main" aria-label="Admin Reported Items">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">Reported Items</h1>
    <p class="page-subtitle">Review reported content, update report status, and contact the seller through report chat.</p>
    <p class="page-subtitle" style="margin-top:8px;">
      <a href="/dashboard/marketplace/admin/sellers" class="action-btn review-btn" style="text-decoration:none;">Open Seller Moderation</a>
    </p>
  </div>

  <!-- Report Tabs -->
  <div class="report-tabs">
    <button class="tab-btn active" data-status="all">All Reports</button>
    <button class="tab-btn" data-status="pending">Pending Review</button>
    <button class="tab-btn" data-status="under-review">Under Review</button>
    <button class="tab-btn" data-status="resolved">Resolved</button>
    <button class="tab-btn" data-status="archived">Archived</button>
  </div>

  <!-- Search and Filters -->
  <div class="search-filters">
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none">
        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
        <path d="m21 21-4.35-4.35" stroke="currentColor" stroke-width="2"/>
      </svg>
      <input type="text" id="search-input" placeholder="Search by item name, reporter, or reason...">
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
      
      <select id="date-filter" class="filter-select">
        <option value="">All Time</option>
        <option value="today">Today</option>
        <option value="week">This Week</option>
        <option value="month">This Month</option>
      </select>
    </div>
  </div>

  <!-- Reports Table -->
  <div class="reports-table-container">
    <table class="reports-table">
      <thead>
        <tr>
          <th>Report ID</th>
          <th>Item Details</th>
          <th>Reporter</th>
          <th>Seller</th>
          <th>Reason</th>
          <th>Date Reported</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="reports-tbody">
        <tr>
          <td colspan="8" class="empty-description">Loading reported items...</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Empty State -->
  <div id="empty-state" class="empty-state" style="display: none;">
    <svg class="empty-icon" viewBox="0 0 24 24" fill="none">
      <path d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <h3 class="empty-title">No reports found</h3>
    <p class="empty-description">No reports match your current filters. Try adjusting your search criteria.</p>
  </div>

  <!-- Pagination -->
  <div class="pagination">
    <button class="page-btn" disabled>Previous</button>
    <button class="page-btn active">1</button>
    <button class="page-btn">2</button>
    <button class="page-btn">3</button>
    <span class="page-dots">...</span>
    <button class="page-btn">10</button>
    <button class="page-btn">Next</button>
  </div>

</main>

<!-- JavaScript -->
<script src="/js/app/admin/marketplace/admin-reported.js"></script>