<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-orders.css">

<!-- Main Orders Page -->
<main class="orders-main" role="main" aria-label="Orders Management">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Orders</h1>
    </div>
  </div>

  <!-- Order Status Tabs -->
  <div class="order-tabs">
    <button class="tab-btn active" data-status="all">All orders</button>
    <button class="tab-btn" data-status="yet-to-ship">Yet to Ship</button>
    <button class="tab-btn" data-status="delivered">Delivered</button>
    <button class="tab-btn" data-status="canceled">Canceled</button>
    <button class="tab-btn" data-status="returned">Returned</button>
  </div>

  <!-- Search and Filters -->
  <div class="search-filters">
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none">
        <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65" stroke="currentColor" stroke-width="2"/>
      </svg>
      <input type="text" id="search-input" placeholder="Search orders by Item name, user, or order ID">
    </div>
    
    <div class="filters">
      <select id="status-filter" class="filter-select">
        <option value="">Status</option>
        <option value="yet-to-ship">Yet to Ship</option>
        <option value="delivered">Delivered</option>
        <option value="canceled">Canceled</option>
        <option value="returned">Returned</option>
      </select>
      
      <select id="date-filter" class="filter-select">
        <option value="">Date</option>
        <option value="today">Today</option>
        <option value="week">This Week</option>
        <option value="month">This Month</option>
      </select>
    </div>
  </div>

  <!-- Orders Table -->
  <div class="orders-table-container">
    <table class="orders-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Item</th>
          <th>User</th>
          <th>Payment Method</th>
          <th>Date placed</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="orders-tbody">
        <!-- Order 1 - Yet to Ship with Preorder -->
        <tr class="order-row" data-status="yet-to-ship" data-payment="preorder" data-order-id="12345">
          <td class="order-id">#12345</td>
          <td class="item-name">UCSC Tshirt</td>
          <td class="user-info">
            <div class="user-name">Dhananjaya Mudalige</div>
          </td>
          <td class="payment-method">
            <span class="payment-badge preorder">Pre-order</span>
          </td>
          <td class="date-placed">2023-07-25</td>
          <td class="status">
            <span class="status-badge yet-to-ship">Yet to ship</span>
          </td>
          <td class="actions">
            <button class="action-btn manage-btn" onclick="manageOrder('#12345', 'yet-to-ship', 'preorder')">Manage</button>
            <button class="action-btn chat-btn" 
                    data-order-id="12345" 
                    data-buyer-name="Dhananjaya Mudalige"
                    onclick="openChat('12345', 'Dhananjaya Mudalige')">
              Chat
              <span class="chat-badge" style="display: none;">2</span>
            </button>
          </td>
        </tr>

        <!-- Order 2 - Yet to Ship with COD -->
        <tr class="order-row" data-status="yet-to-ship" data-payment="cod" data-order-id="12346">
          <td class="order-id">#12346</td>
          <td class="item-name">UCSC Wrist band</td>
          <td class="user-info">
            <div class="user-name">Amasha</div>
          </td>
          <td class="payment-method">
            <span class="payment-badge cod">Cash on Delivery</span>
          </td>
          <td class="date-placed">2023-07-25</td>
          <td class="status">
            <span class="status-badge yet-to-ship">Yet to ship</span>
          </td>
          <td class="actions">
            <button class="action-btn manage-btn" onclick="manageOrder('#12346', 'yet-to-ship', 'cod')">Manage</button>
            <button class="action-btn chat-btn" 
                    data-order-id="12346" 
                    data-buyer-name="Amasha"
                    onclick="openChat('12346', 'Amasha')">
              Chat
            </button>
          </td>
        </tr>

        <!-- Order 3 - Delivered -->
        <tr class="order-row" data-status="delivered" data-payment="preorder" data-order-id="12347">
          <td class="order-id">#12347</td>
          <td class="item-name">DSA Book</td>
          <td class="user-info">
            <div class="user-name">Dhananjaya Mudalige</div>
          </td>
          <td class="payment-method">
            <span class="payment-badge preorder">Pre-order</span>
          </td>
          <td class="date-placed">2023-07-26</td>
          <td class="status">
            <span class="status-badge delivered">Delivered</span>
          </td>
          <td class="actions">
            <button class="action-btn view-btn" onclick="viewOrder('#12347')">View</button>
            <button class="action-btn chat-btn" 
                    data-order-id="12347" 
                    data-buyer-name="Dhananjaya Mudalige"
                    onclick="openChat('12347', 'Dhananjaya Mudalige')">
              Chat
            </button>
          </td>
        </tr>

        <!-- Order 4 - Canceled -->
        <tr class="order-row" data-status="canceled" data-payment="cod" data-order-id="12348">
          <td class="order-id">#12348</td>
          <td class="item-name">Laptop Charger</td>
          <td class="user-info">
            <div class="user-name">Amasha</div>
          </td>
          <td class="payment-method">
            <span class="payment-badge cod">Cash on Delivery</span>
          </td>
          <td class="date-placed">2023-07-25</td>
          <td class="status">
            <span class="status-badge canceled">Canceled</span>
          </td>
          <td class="actions">
            <button class="action-btn view-btn" onclick="viewOrder('#12348')">View</button>
            <button class="action-btn chat-btn" 
                    data-order-id="12348" 
                    data-buyer-name="Amasha"
                    onclick="openChat('12348', 'Amasha')">
              Chat
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Empty State -->
  <div class="empty-state" id="empty-state" style="display: none;">
    <div class="empty-icon">
      <svg viewBox="0 0 24 24" fill="none">
        <path d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z" stroke="currentColor" stroke-width="2"/>
      </svg>
    </div>
    <h3 class="empty-title">No Orders Found</h3>
    <p class="empty-description">No orders match your current filters.</p>
  </div>

  <!-- Pagination -->
  <div class="pagination">
    <button class="page-btn" id="prev-btn" disabled>‹</button>
    <span class="page-numbers">
      <button class="page-btn active">1</button>
      <button class="page-btn">2</button>
      <button class="page-btn">3</button>
      <span class="page-dots">...</span>
    </span>
    <button class="page-btn" id="next-btn">›</button>
  </div>

</main>

<!-- Order Management Modal -->
<div class="modal-overlay" id="manage-modal" style="display: none;">
  <div class="modal manage-modal">
    <div class="modal-header">
      <h3 id="manage-title">Manage Order</h3>
      <button class="close-btn" onclick="closeManageModal()">×</button>
    </div>
    <div class="modal-body">
      <div class="order-details" id="order-details">
        <!-- Order details will be populated here -->
      </div>
      
      <!-- Payment Slip Section (for preorder only) -->
      <div class="payment-slip-section" id="payment-slip-section" style="display: none;">
        <h4>Payment Slip</h4>
        <div class="payment-slip-viewer">
          <img id="payment-slip-image" src="" alt="Payment Slip" style="max-width: 100%; border-radius: 8px;">
        </div>
      </div>
      
      <!-- Actions Section -->
      <div class="manage-actions">
        <button class="action-btn delivery-btn" onclick="markAsDelivered()">
          <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
            <path d="M5 12l5 5L20 7" stroke="currentColor" stroke-width="2"/>
          </svg>
          Mark as Delivered
        </button>
        
        <button class="action-btn cancel-btn" onclick="showCancelForm()">
          <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2"/>
            <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2"/>
          </svg>
          Cancel Order
        </button>
      </div>
      
      <!-- Cancel Form (hidden by default) -->
      <div class="cancel-form" id="cancel-form" style="display: none;">
        <h4>Cancel Order</h4>
        <div class="form-group">
          <label for="cancel-reason">Reason for cancellation *</label>
          <textarea id="cancel-reason" placeholder="Please provide a reason for canceling this order..." rows="4" required></textarea>
        </div>
        <div class="form-actions">
          <button class="btn btn-secondary" onclick="hideCancelForm()">Back</button>
          <button class="btn btn-danger" onclick="confirmCancelOrder()">Cancel Order</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Chat Modal -->
<div class="chat-modal" id="chat-modal" aria-hidden="true" role="dialog" aria-labelledby="chat-modal-title">
  <div class="chat-modal-backdrop" aria-label="Close chat"></div>
  <div class="chat-modal-container">
    
    <!-- Chat Header -->
    <div class="chat-header">
      <div class="chat-info">
        <div class="buyer-avatar">
          <img src="/images/placeholders/user.png" alt="Buyer" id="buyer-avatar">
        </div>
        <div class="chat-details">
          <h2 class="chat-modal-title" id="chat-modal-title">Chat with Buyer</h2>
          <div class="order-info">
            <span class="order-title" id="chat-order-title">Order Title</span>
            <span class="order-id" id="chat-order-id">#12345</span>
          </div>
        </div>
      </div>
      <button class="chat-close" aria-label="Close chat">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>
    </div>

    <!-- Chat Messages -->
    <div class="chat-messages" id="chat-messages">
      <div class="chat-loading">
        <div class="loading-spinner"></div>
        <span>Loading messages...</span>
      </div>
    </div>

    <!-- Chat Input -->
    <div class="chat-input-container">
      <form class="chat-form" id="chat-form">
        <div class="chat-input-wrapper">
          <textarea 
            class="chat-input" 
            id="chat-input" 
            placeholder="Type your message..."
            rows="1"
            maxlength="500"
          ></textarea>
          <button type="submit" class="chat-send" aria-label="Send message">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        <div class="chat-actions">
          <div class="char-counter">
            <span id="chat-char-count">0</span>/500
          </div>
        </div>
      </form>
    </div>

  </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading-overlay" style="display: none;">
  <div class="loading-spinner">
    <div class="spinner"></div>
    <p>Processing...</p>
  </div>
</div>

<!-- JavaScript -->
<script src="/js/app/marketplace/seller-portal-orders.js"></script>