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
    </div>
  </nav>

  <!-- Lost Items Section -->
  <div class="lf-section active" id="lf-lost-items">
    <div class="section-header">
      <h2 class="section-title">Manage Lost Items</h2>
      <div class="section-stats">
        <span class="stat-item">
          <span class="stat-number">0</span>
          <span class="stat-label">All Items</span>
        </span>
        <span class="stat-item">
          <span class="stat-number">0</span>
          <span class="stat-label">Active</span>
        </span>
        <span class="stat-item">
          <span class="stat-number">0</span>
          <span class="stat-label">Resolved</span>
        </span>
      </div>
    </div>

    <!-- Lost Items Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-tab active" onclick="filterLostItems('all', event)">All Items</button>
      <button class="filter-tab" onclick="filterLostItems('active', event)">Active Items</button>
      <button class="filter-tab" onclick="filterLostItems('resolved', event)">Resolved Items</button>
    </div>

    <!-- Lost Items Content -->
    <div class="items-container">
      <div class="items-grid active" id="lost-items-all">
        <!-- Items will be loaded here by JavaScript -->
      </div>
      <div class="items-grid" id="lost-items-active" style="display: none;">
        <!-- Active items will be loaded here -->
      </div>
      <div class="items-grid" id="lost-items-resolved" style="display: none;">
        <!-- Resolved items will be loaded here -->
      </div>
    </div>
  </div>

  <!-- Found Items Section -->
  <div class="lf-section" id="lf-found-items">
    <div class="section-header">
      <h2 class="section-title">Manage Found Items</h2>
      <div class="section-stats">
        <span class="stat-item">
          <span class="stat-number">0</span>
          <span class="stat-label">All Items</span>
        </span>
        <span class="stat-item">
          <span class="stat-number">0</span>
          <span class="stat-label">Active</span>
        </span>
        <span class="stat-item">
          <span class="stat-number">0</span>
          <span class="stat-label">Returned</span>
        </span>
      </div>
    </div>

    <!-- Found Items Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-tab active" onclick="filterFoundItems('all', event)">All Items</button>
      <button class="filter-tab" onclick="filterFoundItems('active', event)">Active Items</button>
      <button class="filter-tab" onclick="filterFoundItems('returned', event)">Returned Items</button>
    </div>

    <!-- Found Items Content -->
    <div class="items-container">
      <div class="items-grid" id="found-items-all" style="display: grid;">
        <div class="empty-state"><p>Loading found items...</p></div>
      </div>
      <div class="items-grid" id="found-items-active" style="display: none;">
        <!-- Active items will be loaded here -->
      </div>
      <div class="items-grid" id="found-items-returned" style="display: none;">
        <!-- Returned items will be loaded here -->
      </div>
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
  <div class="modal modal-form">
    <button class="modal-close" onclick="closeNewReportModal()">×</button>
    
    <div class="modal-section">
      <div class="modal-section-title">Create New Report</div>
      <p class="modal-subtitle">Submit a lost or found item report on behalf of a user</p>
      <div class="modal-section-content">
        <form class="new-report-form" id="new-report-form" enctype="multipart/form-data">
          <div class="form-error-global" id="form-error-global" style="display: none;"></div>
          
          <div class="form-group">
            <label>Report Type *</label>
            <select class="form-control" id="report-type" name="type" required>
              <option value="lost">Lost Item</option>
              <option value="found">Found Item</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Item Name *</label>
            <input type="text" class="form-control" id="item-name" name="item_name" placeholder="e.g., Backpack, Laptop, Phone" required maxlength="100">
          </div>
          
          <div class="form-group">
            <label>Category *</label>
            <select class="form-control" id="category" name="category" required>
              <option value="">Select Category</option>
              <option value="electronics">Electronics</option>
              <option value="bags">Bags & Backpacks</option>
              <option value="clothing">Clothing</option>
              <option value="accessories">Accessories</option>
              <option value="books">Books & Stationery</option>
              <option value="documents">Documents & IDs</option>
              <option value="keys">Keys</option>
              <option value="jewelry">Jewelry</option>
              <option value="sports">Sports Equipment</option>
              <option value="other">Other</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Description *</label>
            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe the item and circumstances in detail..." required maxlength="500"></textarea>
            <div class="char-count"><span id="desc-char-count">0</span>/500</div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label>Location *</label>
              <input type="text" class="form-control" id="location" name="location" placeholder="Where was it lost/found?" required maxlength="100">
            </div>
            <div class="form-group">
              <label>Date *</label>
              <input type="date" class="form-control" id="incident-date" name="incident_date" required max="<?php echo date('Y-m-d'); ?>">
            </div>
          </div>
          
          <div class="form-group">
            <label>Contact Email *</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="contact@example.com" required>
          </div>
          
          <div class="form-group">
            <label>Contact Phone *</label>
            <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="+94 71 234 5678" required maxlength="15">
          </div>
          
          <div class="form-group">
            <label>Upload Image (Optional)</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <small style="color: #6b7280; font-size: 12px;">Max 5MB, JPG/PNG/WebP</small>
          </div>
          
          <div class="form-actions">
            <button type="button" class="modal-btn btn-secondary" onclick="closeNewReportModal()">Cancel</button>
            <button type="submit" class="modal-btn btn-primary" id="submit-new-report">Create Report</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="/js/app/admin/lost-and-found.js"></script>

<style>
/* Additional styles for dynamic content */
.status-badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 500;
  display: inline-block;
  margin-top: 4px;
}

.status-active {
  background-color: #dbeafe;
  color: #1e40af;
}

.status-resolved {
  background-color: #d1fae5;
  color: #065f46;
}

.status-pending {
  background-color: #fef3c7;
  color: #92400e;
}

.empty-state {
  grid-column: 1 / -1;
  padding: 60px 20px;
  text-align: center;
  color: #6b7280;
  font-size: 16px;
}

.item-card {
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  background: white;
}

.item-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.item-image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  background-size: cover;
  background-position: center;
  background-color: #f3f4f6;
  flex-shrink: 0;
}

.item-meta {
  font-size: 12px;
  color: #6b7280;
  margin-top: 8px;
}

.modal-overlay.active {
  display: flex;
}

.action-btn {
  padding: 6px 12px;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.2s;
}

.action-btn:hover {
  background-color: #2563eb;
}

.items-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 16px;
  padding: 16px 0;
}

.item-card {
  display: flex;
  gap: 12px;
  align-items: flex-start;
}

.item-info {
  flex: 1;
  min-width: 0;
}

.item-title {
  font-weight: 600;
  font-size: 16px;
  margin-bottom: 4px;
}

.item-description {
  font-size: 14px;
  color: #6b7280;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #3b82f6;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
  flex-shrink: 0;
}

/* New Report Modal Form Styles */
.modal-form {
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-subtitle {
  color: #6b7280;
  font-size: 14px;
  margin-bottom: 20px;
}

.new-report-form .form-group {
  margin-bottom: 16px;
}

.new-report-form label {
  display: block;
  font-weight: 500;
  margin-bottom: 6px;
  color: #374151;
  font-size: 14px;
}

.new-report-form .form-control {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.new-report-form .form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.new-report-form textarea.form-control {
  resize: vertical;
  min-height: 100px;
}

.new-report-form select.form-control {
  cursor: pointer;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.char-count {
  text-align: right;
  font-size: 12px;
  color: #6b7280;
  margin-top: 4px;
}

.form-error-global {
  background: #fee;
  border: 1px solid #fcc;
  color: #c33;
  padding: 12px;
  border-radius: 6px;
  margin-bottom: 16px;
  font-size: 14px;
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

@media (max-width: 640px) {
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .modal-form {
    max-width: 95%;
  }
}
</style>