<!-- filepath: /home/dhananjaya/Documents/projects/reidhub/app/views/User/marketplace/transactions-view.php -->
<link rel="stylesheet" href="/css/app/user/marketplace/all-transactions.css">

<!-- Main Content Area -->
<main class="transactions-main" role="main" aria-label="My Transactions">
  <div class="container">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">My Transactions</h1>
        <div class="transactions-count" id="transactions-count">Loading transactions...</div>
      </div>
      
      <!-- Filters and Search -->
      <div class="transactions-filters">
        <div class="search-container">
          <input type="text" id="search-input" class="search-input" placeholder="Search transactions..." aria-label="Search transactions">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
        </div>
        
        <select id="status-filter" class="filter-select" aria-label="Filter by status">
          <option value="">All Statuses</option>
          <option value="yet_to_ship">Yet to Ship</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
        
        <select id="payment-filter" class="filter-select" aria-label="Filter by payment method">
          <option value="">All Payment Methods</option>
          <option value="cash_on_delivery">Cash on Delivery</option>
          <option value="preorder">Pre-order</option>
        </select>
        
        <select id="date-filter" class="filter-select" aria-label="Filter by date">
          <option value="">All Time</option>
          <option value="today">Today</option>
          <option value="week">This Week</option>
          <option value="month">This Month</option>
          <option value="year">This Year</option>
        </select>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="content-tabs" role="tablist">
      <button class="tab-button tab-button--active" data-tab="all" role="tab" aria-selected="true" tabindex="0">
        All Transactions
        <span class="tab-count" id="count-all">0</span>
      </button>
      <button class="tab-button" data-tab="recent" role="tab" aria-selected="false" tabindex="-1">
        Recent
        <span class="tab-count" id="count-recent">0</span>
      </button>
      <button class="tab-button" data-tab="completed" role="tab" aria-selected="false" tabindex="-1">
        Completed
        <span class="tab-count" id="count-completed">0</span>
      </button>
      <button class="tab-button" data-tab="pending" role="tab" aria-selected="false" tabindex="-1">
        Pending
        <span class="tab-count" id="count-pending">0</span>
      </button>
      <button class="tab-button" data-tab="cancelled" role="tab" aria-selected="false" tabindex="-1">
        Cancelled
        <span class="tab-count" id="count-cancelled">0</span>
      </button>
    </div>

    <!-- Transactions Table -->
    <div class="transactions-section">
      <div class="table-container">
        <table class="transactions-table" role="table">
          <thead>
            <tr>
              <th class="sortable" data-sort="id" role="columnheader" tabindex="0">
                Transaction ID
                <svg class="sort-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M7 10l5-5 5 5M7 14l5 5 5-5"/>
                </svg>
              </th>
              <th class="sortable" data-sort="date" role="columnheader" tabindex="0">
                Date
                <svg class="sort-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M7 10l5-5 5 5M7 14l5 5 5-5"/>
                </svg>
              </th>
              <th role="columnheader">Items</th>
              <th class="sortable" data-sort="total" role="columnheader" tabindex="0">
                Total Amount
                <svg class="sort-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M7 10l5-5 5 5M7 14l5 5 5-5"/>
                </svg>
              </th>
              <th role="columnheader">Payment Method</th>
              <th role="columnheader">Status</th>
              <th role="columnheader">Actions</th>
            </tr>
          </thead>
          <tbody id="transactions-tbody">
            <!-- Sample rows for styling - will be replaced by JavaScript -->
            <tr class="transaction-row" data-transaction-id="1001">
              <td class="transaction-id">#TX1001</td>
              <td class="transaction-date">
                <time datetime="2024-01-25">Jan 25, 2024</time>
                <span class="time-ago">2 days ago</span>
              </td>
              <td class="transaction-items">
                <div class="items-summary">
                  <div class="item-count">3 items</div>
                  <div class="item-preview">Custom T-Shirt, Leather Band, Book</div>
                </div>
              </td>
              <td class="transaction-total">
                <div class="amount">$78.49</div>
              </td>
              <td class="payment-method">
                <div class="payment-badges">
                  <span class="payment-badge payment-badge--cod">Cash on Delivery</span>
                  <span class="payment-badge payment-badge--preorder">Pre-order</span>
                </div>
              </td>
              <td class="transaction-status">
                <div class="status-summary">
                  <span class="status-badge status-badge--delivered">2 Delivered</span>
                  <span class="status-badge status-badge--pending">1 Pending</span>
                </div>
              </td>
              <td class="transaction-actions">
                <button class="action-btn action-btn--primary" onclick="viewTransaction('1001')">
                  View Details
                </button>
                <button class="action-btn action-btn--secondary" onclick="downloadInvoice('1001')">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                  </svg>
                  Invoice
                </button>
              </td>
            </tr>

            <tr class="transaction-row" data-transaction-id="1002">
              <td class="transaction-id">#TX1002</td>
              <td class="transaction-date">
                <time datetime="2024-01-24">Jan 24, 2024</time>
                <span class="time-ago">3 days ago</span>
              </td>
              <td class="transaction-items">
                <div class="items-summary">
                  <div class="item-count">1 item</div>
                  <div class="item-preview">Handmade Bracelet</div>
                </div>
              </td>
              <td class="transaction-total">
                <div class="amount">$22.75</div>
              </td>
              <td class="payment-method">
                <div class="payment-badges">
                  <span class="payment-badge payment-badge--preorder">Pre-order</span>
                </div>
              </td>
              <td class="transaction-status">
                <div class="status-summary">
                  <span class="status-badge status-badge--cancelled">1 Cancelled</span>
                </div>
              </td>
              <td class="transaction-actions">
                <button class="action-btn action-btn--primary" onclick="viewTransaction('1002')">
                  View Details
                </button>
                <button class="action-btn action-btn--secondary" onclick="downloadInvoice('1002')">
                  <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                  </svg>
                  Invoice
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div class="empty-state" id="empty-state" style="display: none;">
        <div class="empty-icon">📋</div>
        <h3>No transactions found</h3>
        <p>You haven't made any transactions yet, or no transactions match your current filters.</p>
        <a href="/marketplace" class="btn btn--primary">Browse Marketplace</a>
      </div>

      <!-- Loading State -->
      <div class="loading-state" id="loading-state" style="display: none;">
        <div class="loading-spinner"></div>
        <p>Loading transactions...</p>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-container" id="pagination-container">
      <div class="pagination-info">
        Showing <span id="showing-start">1</span>-<span id="showing-end">10</span> of <span id="total-transactions">25</span> transactions
      </div>
      <div class="pagination-controls">
        <button class="page-btn page-btn--prev" id="prev-btn" disabled>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="15,18 9,12 15,6"/>
          </svg>
          Previous
        </button>
        <div class="page-numbers" id="page-numbers">
          <button class="page-btn page-btn--active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
        </div>
        <button class="page-btn page-btn--next" id="next-btn">
          Next
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <polyline points="9,18 15,12 9,6"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</main>

<script src="/js/app/marketplace/all-transactions.js"></script>