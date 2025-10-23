<link rel="stylesheet" href="/css/app/admin/marketplace/reported.css">

<!-- Main Reported Items Dashboard -->
<main class="reported-main" role="main" aria-label="Admin Reported Items">
  
  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">Reported Items</h1>
    <p class="page-subtitle">Manage reported marketplace items and take appropriate actions.</p>
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
        <!-- Sample Data - Replace with dynamic content -->
        <tr class="report-row" data-status="pending" data-category="inappropriate" data-hidden="true">
          <td class="report-id">#RPT-001</td>
          <td class="item-details">
            <div class="item-info">
              <img src="/assets/placeholders/product.jpeg" alt="Item" class="item-image">
              <div class="item-text">
                <div class="item-name">Inappropriate T-Shirt Design</div>
                <div class="item-price">Rs. 1,500</div>
              </div>
              <span class="hidden-indicator">👁️‍🗨️ Hidden</span>
            </div>
          </td>
          <td class="reporter-info">
            <div class="user-name">John Doe</div>
            <div class="user-email">john@example.com</div>
          </td>
          <td class="seller-info">
            <div class="user-name">Alice Cooper</div>
            <div class="user-email">alice@example.com</div>
          </td>
          <td class="report-reason">
            <span class="reason-tag inappropriate">Inappropriate Content</span>
            <div class="reason-text">Contains offensive imagery</div>
          </td>
          <td class="date-reported">Oct 22, 2024</td>
          <td class="status">
            <span class="status-badge pending">Pending Review</span>
          </td>
          <td class="actions">
            <button class="action-btn review-btn" onclick="reviewReport('#RPT-001', 'pending')">Review</button>
            <button class="action-btn chat-btn" onclick="contactSeller('Alice Cooper', 'alice@example.com')">Contact Seller</button>
            <button class="action-btn show-btn" onclick="showItem('#RPT-001')">Show Item</button>
            <button class="action-btn archive-btn" onclick="archiveReport('#RPT-001')">Archive</button>
          </td>
        </tr>

        <tr class="report-row" data-status="under-review" data-category="spam">
          <td class="report-id">#RPT-002</td>
          <td class="item-details">
            <div class="item-info">
              <img src="/assets/placeholders/product.jpeg" alt="Item" class="item-image">
              <div class="item-text">
                <div class="item-name">Spam Product Listing</div>
                <div class="item-price">Rs. 500</div>
              </div>
            </div>
          </td>
          <td class="reporter-info">
            <div class="user-name">Jane Smith</div>
            <div class="user-email">jane@example.com</div>
          </td>
          <td class="seller-info">
            <div class="user-name">Bob Wilson</div>
            <div class="user-email">bob@example.com</div>
          </td>
          <td class="report-reason">
            <span class="reason-tag spam">Spam</span>
            <div class="reason-text">Duplicate listings flooding the marketplace</div>
          </td>
          <td class="date-reported">Oct 21, 2024</td>
          <td class="status">
            <span class="status-badge under-review">Under Review</span>
          </td>
          <td class="actions">
            <button class="action-btn review-btn" onclick="reviewReport('#RPT-002', 'under-review')">Review</button>
            <button class="action-btn chat-btn" onclick="contactSeller('Bob Wilson', 'bob@example.com')">Contact Seller</button>
            <button class="action-btn archive-btn" onclick="archiveReport('#RPT-002')">Archive</button>
          </td>
        </tr>

        <tr class="report-row" data-status="resolved" data-category="fraud">
          <td class="report-id">#RPT-003</td>
          <td class="item-details">
            <div class="item-info">
              <img src="/assets/placeholders/product.jpeg" alt="Item" class="item-image">
              <div class="item-text">
                <div class="item-name">Fraudulent Jersey</div>
                <div class="item-price">Rs. 2,000</div>
              </div>
            </div>
          </td>
          <td class="reporter-info">
            <div class="user-name">Mike Davis</div>
            <div class="user-email">mike@example.com</div>
          </td>
          <td class="seller-info">
            <div class="user-name">Carol Brown</div>
            <div class="user-email">carol@example.com</div>
          </td>
          <td class="report-reason">
            <span class="reason-tag fraud">Fraud/Scam</span>
            <div class="reason-text">Selling counterfeit merchandise</div>
          </td>
          <td class="date-reported">Oct 20, 2024</td>
          <td class="status">
            <span class="status-badge resolved">Resolved</span>
          </td>
          <td class="actions">
            <button class="action-btn view-btn" onclick="viewReport('#RPT-003')">View</button>
            <button class="action-btn chat-btn" onclick="contactSeller('Carol Brown', 'carol@example.com')">Contact Seller</button>
            <button class="action-btn archive-btn" onclick="archiveReport('#RPT-003')">Archive</button>
          </td>
        </tr>

        <tr class="report-row" data-status="archived" data-category="other">
          <td class="report-id">#RPT-004</td>
          <td class="item-details">
            <div class="item-info">
              <img src="/assets/placeholders/product.jpeg" alt="Item" class="item-image">
              <div class="item-text">
                <div class="item-name">Archived Report Item</div>
                <div class="item-price">Rs. 800</div>
              </div>
            </div>
          </td>
          <td class="reporter-info">
            <div class="user-name">Sarah Johnson</div>
            <div class="user-email">sarah@example.com</div>
          </td>
          <td class="seller-info">
            <div class="user-name">David Lee</div>
            <div class="user-email">david@example.com</div>
          </td>
          <td class="report-reason">
            <span class="reason-tag other">Other</span>
            <div class="reason-text">Item description misleading</div>
          </td>
          <td class="date-reported">Oct 19, 2024</td>
          <td class="status">
            <span class="status-badge archived">Archived</span>
          </td>
          <td class="actions">
            <button class="action-btn view-btn" onclick="viewReport('#RPT-004')">View</button>
            <button class="action-btn chat-btn" onclick="contactSeller('David Lee', 'david@example.com')">Contact Seller</button>
            <button class="action-btn unarchive-btn" onclick="unarchiveReport('#RPT-004')">Unarchive</button>
          </td>
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

<!-- Review Report Modal -->
<div id="review-modal" class="modal-overlay">
  <div class="review-modal">
    <div class="modal-header">
      <h3 id="review-title">Review Report</h3>
      <button class="close-btn" onclick="closeReviewModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div id="report-details" class="report-details">
        <!-- Report details will be populated here -->
      </div>
      
      <div class="review-actions">
        <button class="action-btn resolve-btn" onclick="resolveReport()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
          </svg>
          Mark as Resolved
        </button>
        <button class="action-btn under-review-btn" onclick="markUnderReview()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            <path d="M12 1v6M12 17v6M4.22 4.22l4.24 4.24M15.54 15.54l4.24 4.24M1 12h6M17 12h6M4.22 19.78l4.24-4.24M15.54 8.46l4.24-4.24" stroke="currentColor" stroke-width="2"/>
          </svg>
          Mark Under Review
        </button>
        <button class="action-btn dismiss-btn" onclick="dismissReport()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
            <path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2"/>
          </svg>
          Dismiss Report
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Contact Seller Modal -->
<div id="contact-modal" class="modal-overlay">
  <div class="contact-modal">
    <div class="modal-header">
      <h3 id="contact-title">Contact Seller</h3>
      <button class="close-btn" onclick="closeContactModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div class="chat-messages" id="chat-messages">
        <div class="message received">
          <div class="message-content">
            <strong>System:</strong> Admin chat session started. You can now communicate with the seller regarding the reported item.
          </div>
          <div class="message-time">Just now</div>
        </div>
      </div>
      
      <div class="chat-input">
        <input type="text" id="message-input" placeholder="Type your message..." />
        <button class="send-btn" onclick="sendMessage()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <polygon points="22,2 15,22 11,13 2,9" stroke="currentColor" stroke-width="2" fill="none"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript -->
<script src="/js/app/admin/marketplace/admin-reported.js"></script>