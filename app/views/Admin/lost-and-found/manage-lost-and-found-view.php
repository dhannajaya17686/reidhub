<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/admin/lost-and-found.css">

<!-- Main Lost & Found Management Content -->
<main class="lf-admin-main" role="main" aria-label="Lost & Found Management">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <div class="header-text">
        <h1 class="page-title">Lost & Found Management</h1>
        <p class="page-description">Manage all lost and found reports submitted by students</p>
      </div>
      <button class="new-report-btn" onclick="openNewReportModal()">
        <span class="btn-icon">+</span>
        New Report
      </button>
    </div>
  </div>

  <!-- Navigation Tabs -->
  <nav class="lf-navigation" role="tablist" aria-label="Lost and Found sections">
    <div class="nav-tabs">
      <button class="nav-tab active" onclick="switchNav('lf-lost-items', event)" role="tab" aria-selected="true">
        Lost Items
      </button>
      <button class="nav-tab" onclick="switchNav('lf-found-items', event)" role="tab" aria-selected="false">
        Found Items
      </button>
      <button class="nav-tab" onclick="switchNav('lf-reports', event)" role="tab" aria-selected="false">
        Reports
      </button>
    </div>
  </nav>

  <!-- Lost Items Section -->
  <div class="lf-section active" id="lf-lost-items">
    <div class="section-header">
      <h2 class="section-title">Manage Lost Items</h2>
      <div class="section-stats">
        <span class="stat-item">
          <span class="stat-number">24</span>
          <span class="stat-label">Active</span>
        </span>
        <span class="stat-item">
          <span class="stat-number">12</span>
          <span class="stat-label">Resolved</span>
        </span>
      </div>
    </div>

    <!-- Lost Items Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-tab active" onclick="filterLostItems('active')">Active Items</button>
      <button class="filter-tab" onclick="filterLostItems('resolved')">Resolved Items</button>
      <button class="filter-tab" onclick="filterLostItems('expired')">Expired Items</button>
    </div>

    <!-- Lost Items Content -->
    <div class="items-container">
      <div class="items-grid active" id="lost-items-active">
        <!-- Items will be loaded here by JavaScript -->
      </div>
      <div class="items-grid" id="lost-items-resolved" style="display: none;">
        <!-- Resolved items will be loaded here -->
      </div>
      <div class="items-grid" id="lost-items-expired" style="display: none;">
        <!-- Expired items will be loaded here -->
      </div>
    </div>
  </div>

  <!-- Found Items Section -->
  <div class="lf-section" id="lf-found-items">
    <div class="section-header">
      <h2 class="section-title">Manage Found Items</h2>
      <div class="section-stats">
        <span class="stat-item">
          <span class="stat-number">18</span>
          <span class="stat-label">Active</span>
        </span>
        <span class="stat-item">
          <span class="stat-number">8</span>
          <span class="stat-label">Returned</span>
        </span>
      </div>
    </div>

    <!-- Found Items Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-tab active" onclick="filterFoundItems('active')">Active Items</button>
      <button class="filter-tab" onclick="filterFoundItems('returned')">Returned Items</button>
      <button class="filter-tab" onclick="filterFoundItems('expired')">Expired Items</button>
    </div>

    <!-- Found Items Content -->
    <div class="items-container">
      <div class="items-grid active" id="found-items-active">
        <!-- Items will be loaded here by JavaScript -->
      </div>
      <div class="items-grid" id="found-items-returned" style="display: none;">
        <!-- Returned items will be loaded here -->
      </div>
      <div class="items-grid" id="found-items-expired" style="display: none;">
        <!-- Expired items will be loaded here -->
      </div>
    </div>
  </div>

  <!-- Reports Section -->
  <div class="lf-section" id="lf-reports">
    <div class="section-header">
      <h2 class="section-title">Reports</h2>
      <p class="section-subtitle">Manage all lost and found reports submitted by students</p>
    </div>

    <!-- Reports Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-tab active" onclick="filterLFReports('all')">All Reports</button>
      <button class="filter-tab" onclick="filterLFReports('lost')">Lost Items</button>
      <button class="filter-tab" onclick="filterLFReports('found')">Found Items</button>
      <button class="filter-tab" onclick="filterLFReports('pending')">Pending Items for Approvals</button>
      <button class="filter-tab" onclick="filterLFReports('rejected')">Rejected</button>
    </div>

    <!-- Search and Filters -->
    <div class="reports-filters">
      <div class="search-box">
        <input type="text" 
               placeholder="Search reports by item name, user, or report ID" 
               class="search-input"
               id="lf-report-search">
        <button class="search-btn">🔍</button>
      </div>
      <div class="filter-controls">
        <select class="filter-select" id="status-filter">
          <option value="">Status</option>
          <option value="missing">Missing</option>
          <option value="returned">Returned</option>
          <option value="collected">Collected</option>
          <option value="pending">Pending</option>
          <option value="rejected">Rejected</option>
        </select>
        <select class="filter-select" id="severity-filter">
          <option value="">Severity</option>
          <option value="high">High</option>
          <option value="medium">Medium</option>
          <option value="low">Low</option>
        </select>
        <select class="filter-select" id="date-filter">
          <option value="">Date</option>
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
            <th>Item</th>
            <th>User</th>
            <th>Date Reported</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="lf-reports-table">
          <!-- Reports will be loaded here by JavaScript -->
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <button class="pagination-btn" disabled>‹</button>
      <button class="pagination-btn active">1</button>
      <button class="pagination-btn">2</button>
      <button class="pagination-btn">3</button>
      <span class="pagination-dots">...</span>
      <button class="pagination-btn">10</button>
      <button class="pagination-btn">›</button>
    </div>
  </div>

</main>

<!-- Item Detail Modal -->
<div class="modal-overlay" id="item-modal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal()">×</button>
    
    <!-- Modal Header -->
    <div class="modal-header">
      <div class="modal-avatar"></div>
      <div class="modal-user-info">
        <h3 id="modal-user-name">Dhananjaya Mudalige</h3>
        <p id="modal-user-year">2nd Year Undergraduate</p>
      </div>
    </div>

    <!-- Item Images -->
    <div class="modal-images" id="modal-images">
      <div class="modal-image"></div>
      <div class="modal-image"></div>
      <div class="modal-image"></div>
    </div>

    <!-- Item Details -->
    <div class="modal-section">
      <div class="modal-section-title" id="modal-item-title">BackPack</div>
      <div class="modal-section-content" id="modal-description">
        Lost on Bawana on October 26, 2024. Black backpack with a laptop and notebooks.
      </div>
    </div>

    <!-- Location and Time Info -->
    <div class="modal-info-grid">
      <div class="modal-info-item">
        <div class="modal-info-label">Last Seen at:</div>
        <div class="modal-info-value" id="modal-location">Bawana</div>
      </div>
      <div class="modal-info-item">
        <div class="modal-info-label">Location:</div>
        <div class="modal-info-value" id="modal-location-2">Bawana(UCSC Canteen)</div>
      </div>
      <div class="modal-info-item">
        <div class="modal-info-label">Lost date & time:</div>
        <div class="modal-info-value">
          <span id="modal-date">26/10/24</span> <span id="modal-time">08:00 P.M</span>
        </div>
      </div>
      <div class="modal-info-item">
        <div class="modal-info-label">Contact:</div>
        <div class="modal-info-value">
          <div id="modal-email">dhananjayamudalige@gmail.com</div>
          <div id="modal-mobile">+94 771234567</div>
        </div>
      </div>
    </div>

    <!-- Modal Actions -->
    <div class="modal-actions" id="modal-actions">
      <button class="modal-btn btn-primary" onclick="pinPost()">Pin post</button>
      <button class="modal-btn btn-success" onclick="markAsResolved()">Mark as resolved</button>
      <button class="modal-btn btn-secondary" onclick="editPost()">Edit Post</button>
      <button class="modal-btn btn-danger" onclick="removePost()">Remove Post</button>
      <button class="modal-btn btn-primary" onclick="contactOwner()">Contact Owner</button>
    </div>
  </div>
</div>

<!-- New Report Modal -->
<div class="modal-overlay" id="new-report-modal">
  <div class="modal">
    <button class="modal-close" onclick="closeNewReportModal()">×</button>
    
    <div class="modal-section">
      <div class="modal-section-title">Create New Report</div>
      <div class="modal-section-content">
        <form class="new-report-form">
          <div class="form-group">
            <label>Report Type</label>
            <select class="form-control">
              <option value="lost">Lost Item</option>
              <option value="found">Found Item</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Item Name</label>
            <input type="text" class="form-control" placeholder="Enter item name">
          </div>
          
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" rows="4" placeholder="Describe the item and circumstances"></textarea>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Location</label>
              <input type="text" class="form-control" placeholder="Where was it lost/found?">
            </div>
            <div class="form-group">
              <label>Date & Time</label>
              <input type="datetime-local" class="form-control">
            </div>
          </div>
          
          <div class="form-group">
            <label>Contact Information</label>
            <input type="email" class="form-control" placeholder="Email address">
            <input type="tel" class="form-control" placeholder="Phone number" style="margin-top: 8px;">
          </div>
          
          <div class="form-actions">
            <button type="button" class="modal-btn btn-secondary" onclick="closeNewReportModal()">Cancel</button>
            <button type="submit" class="modal-btn btn-primary">Create Report</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="/js/app/admin/lost-and-found.js"></script>