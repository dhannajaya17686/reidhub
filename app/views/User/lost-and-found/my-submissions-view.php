<link rel="stylesheet" href="/css/app/user/lost-and-found/my-submissions.css">

<!-- Main Content Area -->
<main class="my-submissions-main" role="main" aria-label="My Lost & Found Submissions">
  <div class="container">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <div class="title-section">
          <h1 class="page-title">My Submissions</h1>
          <p class="page-subtitle">Manage your lost and found item reports</p>
        </div>
        
        <div class="header-actions">
          <a href="/dashboard/lost-and-found/report-lost-item" class="btn btn--secondary">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            Report Lost Item
          </a>
          <a href="/dashboard/lost-and-found/report-found-item" class="btn btn--primary">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            Report Found Item
          </a>
        </div>
      </div>
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['success']) && $_GET['success'] === 'true'): ?>
      <div class="alert alert--success" id="success-alert">
        <svg class="alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <div class="alert-content">
          <strong>Success!</strong> Your report has been submitted successfully.
        </div>
        <button class="alert-close" onclick="closeAlert()">×</button>
      </div>
    <?php endif; ?>

    <!-- Tab Navigation -->
    <div class="content-tabs" role="tablist">
      <button class="tab-button tab-button--active" data-tab="all" role="tab" aria-selected="true">
        All My Items
        <span class="tab-count" id="count-all">0</span>
      </button>
      <button class="tab-button" data-tab="lost" role="tab" aria-selected="false">
        Lost Items
        <span class="tab-count" id="count-lost">0</span>
      </button>
      <button class="tab-button" data-tab="found" role="tab" aria-selected="false">
        Found Items
        <span class="tab-count" id="count-found">0</span>
      </button>
      <button class="tab-button" data-tab="resolved" role="tab" aria-selected="false">
        Resolved
        <span class="tab-count" id="count-resolved">0</span>
      </button>
    </div>

    <!-- Items Grid -->
    <div class="items-section">
      <div class="items-grid" id="items-grid">
        <!-- Loading spinner -->
        <div class="loading-spinner">
          <div class="spinner"></div>
          <p>Loading your submissions...</p>
        </div>
      </div>

      <!-- Empty State -->
      <div class="empty-state" id="empty-state" style="display: none;">
        <div class="empty-icon">📋</div>
        <h3>No submissions yet</h3>
        <p>You haven't reported any lost or found items yet. Start by reporting an item below.</p>
        <div class="empty-actions">
          <a href="/dashboard/lost-and-found/report-lost-item" class="btn btn--primary">
            Report Lost Item
          </a>
          <a href="/dashboard/lost-and-found/report-found-item" class="btn btn--secondary">
            Report Found Item
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Edit Status Modal -->
<div class="modal-overlay" id="status-modal" aria-hidden="true" role="dialog">
  <div class="modal-backdrop"></div>
  <div class="modal-container">
    <div class="modal-header">
      <h2 class="modal-title">Update Item Status</h2>
      <button class="modal-close" onclick="closeStatusModal()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>
    
    <div class="modal-content">
      <form class="status-form" id="status-form">
        <input type="hidden" id="status-item-id" name="item_id">
        
        <div class="form-group">
          <label for="status-select">New Status</label>
          <select id="status-select" name="status" required>
            <option value="">Select status...</option>
            <option value="Still Missing">Still Missing</option>
            <option value="Returned">Returned (Item recovered)</option>
          </select>
        </div>
        
        <div class="form-actions">
          <button type="button" class="btn btn--secondary" onclick="closeStatusModal()">Cancel</button>
          <button type="submit" class="btn btn--primary">Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="/js/app/lost-and-found/my-submissions.js"></script>

<style>
.alert {
  display: flex;
  align-items: center;
  padding: 16px 20px;
  margin-bottom: 24px;
  border-radius: 8px;
  animation: slideDown 0.3s ease-out;
}

.alert--success {
  background-color: #d1fae5;
  border: 1px solid #10b981;
  color: #065f46;
}

.alert-icon {
  width: 24px;
  height: 24px;
  margin-right: 12px;
  stroke-width: 2;
}

.alert-content {
  flex: 1;
}

.alert-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: inherit;
  padding: 0 8px;
  line-height: 1;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.loading-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.spinner {
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
function closeAlert() {
  const alert = document.getElementById('success-alert');
  if (alert) {
    alert.style.animation = 'slideDown 0.3s ease-out reverse';
    setTimeout(() => alert.remove(), 300);
  }
}

// Auto-hide success message after 5 seconds
setTimeout(closeAlert, 5000);
</script>
