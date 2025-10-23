<link rel="stylesheet" href="/css/app/admin/marketplace/analytics.css">

<!-- Main Admin Analytics Dashboard -->
<main class="admin-analytics-main" role="main" aria-label="Admin Marketplace Analytics">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Marketplace Analytics</h1>
      <p class="page-subtitle">Monitor marketplace performance, sales trends, and user activity.</p>
    </div>
    
    <!-- Header Actions -->
    <div class="header-actions">
      <div class="date-filter-group">
        <button class="date-filter-btn active" data-range="week">This Week</button>
        <button class="date-filter-btn" data-range="month">This Month</button>
        <button class="date-filter-btn" data-range="quarter">This Quarter</button>
        <button class="date-filter-btn" data-range="year">This Year</button>
      </div>
      
      <div class="action-buttons">
        <button class="btn btn--secondary" id="refresh-data">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 3v5h-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M3 21v-5h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Refresh
        </button>
        <button class="btn btn--primary" id="export-data">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Export Report
        </button>
      </div>
    </div>
  </div>

  <!-- Key Metrics Overview -->
  <section class="metrics-overview">
    <div class="metrics-grid">
      <!-- Total Revenue -->
      <div class="metric-card metric-card--success">
        <div class="metric-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="metric-content">
          <div class="metric-label">Total Revenue</div>
          <div class="metric-value" id="total-revenue">Rs. 125,450</div>
          <div class="metric-change metric-change--positive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <polyline points="23,6 13.5,15.5 8.5,10.5 1,18" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            +18.5%
          </div>
        </div>
      </div>

      <!-- Total Orders -->
      <div class="metric-card metric-card--primary">
        <div class="metric-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" stroke="currentColor" stroke-width="2" fill="none"/>
          </svg>
        </div>
        <div class="metric-content">
          <div class="metric-label">Total Orders</div>
          <div class="metric-value" id="total-orders">1,248</div>
          <div class="metric-change metric-change--positive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <polyline points="23,6 13.5,15.5 8.5,10.5 1,18" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            +12.3%
          </div>
        </div>
      </div>

      <!-- Active Sellers -->
      <div class="metric-card metric-card--info">
        <div class="metric-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" fill="none"/>
          </svg>
        </div>
        <div class="metric-content">
          <div class="metric-label">Active Sellers</div>
          <div class="metric-value" id="active-sellers">156</div>
          <div class="metric-change metric-change--positive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <polyline points="23,6 13.5,15.5 8.5,10.5 1,18" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            +8.7%
          </div>
        </div>
      </div>

      <!-- Commission Earned -->
      <div class="metric-card metric-card--warning">
        <div class="metric-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
            <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 18V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="metric-content">
          <div class="metric-label">Commission Earned</div>
          <div class="metric-value" id="commission-earned">Rs. 15,680</div>
          <div class="metric-change metric-change--positive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <polyline points="23,6 13.5,15.5 8.5,10.5 1,18" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            +22.1%
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Charts Section -->
  <section class="charts-section">
    <!-- Revenue & Orders Charts -->
    <div class="charts-row">
      <!-- Revenue Trend Chart -->
      <div class="chart-card chart-card--large">
        <div class="chart-header">
          <h3 class="chart-title">Revenue Trend</h3>
          <div class="chart-legend">
            <span class="legend-item">
              <span class="legend-color" style="background: #10B981;"></span>
              Revenue
            </span>
          </div>
        </div>
        <div class="chart-container">
          <canvas id="revenue-chart"></canvas>
        </div>
      </div>

      <!-- Orders Chart -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Orders Overview</h3>
        </div>
        <div class="chart-container">
          <canvas id="orders-chart"></canvas>
        </div>
      </div>
    </div>

    <!-- Categories & Sellers Performance -->
    <div class="charts-row">
      <!-- Top Categories -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Top Categories</h3>
        </div>
        <div class="chart-container">
          <canvas id="categories-chart"></canvas>
        </div>
      </div>

      <!-- Top Sellers -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Top Sellers</h3>
        </div>
        <div class="chart-container">
          <canvas id="sellers-chart"></canvas>
        </div>
      </div>
    </div>
  </section>

  <!-- Data Tables Section -->
  <section class="data-section">
    <div class="data-row">
      <!-- Recent Orders -->
      <div class="data-card">
        <div class="data-header">
          <h3 class="data-title">Recent Orders</h3>
          <button class="btn btn--small btn--outline">View All</button>
        </div>
        <div class="data-content">
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><span class="order-id">#ORD-2024-001</span></td>
                  <td>
                    <div class="customer-info">
                      <img src="https://via.placeholder.com/32x32/0466C8/ffffff?text=JD" alt="John Doe" class="customer-avatar">
                      <span>John Doe</span>
                    </div>
                  </td>
                  <td><strong>Rs. 2,450</strong></td>
                  <td><span class="status status--completed">Completed</span></td>
                  <td>Oct 22, 2024</td>
                </tr>
                <tr>
                  <td><span class="order-id">#ORD-2024-002</span></td>
                  <td>
                    <div class="customer-info">
                      <img src="https://via.placeholder.com/32x32/10B981/ffffff?text=JS" alt="Jane Smith" class="customer-avatar">
                      <span>Jane Smith</span>
                    </div>
                  </td>
                  <td><strong>Rs. 1,800</strong></td>
                  <td><span class="status status--pending">Pending</span></td>
                  <td>Oct 22, 2024</td>
                </tr>
                <tr>
                  <td><span class="order-id">#ORD-2024-003</span></td>
                  <td>
                    <div class="customer-info">
                      <img src="https://via.placeholder.com/32x32/F59E0B/ffffff?text=MW" alt="Mike Wilson" class="customer-avatar">
                      <span>Mike Wilson</span>
                    </div>
                  </td>
                  <td><strong>Rs. 3,200</strong></td>
                  <td><span class="status status--processing">Processing</span></td>
                  <td>Oct 21, 2024</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Top Products -->
      <div class="data-card">
        <div class="data-header">
          <h3 class="data-title">Top Products</h3>
          <button class="btn btn--small btn--outline">View All</button>
        </div>
        <div class="data-content">
          <div class="products-list">
            <div class="product-row">
              <div class="product-info">
                <img src="https://via.placeholder.com/48x48/0466C8/ffffff?text=TS" alt="UCSC T-Shirt" class="product-image">
                <div class="product-details">
                  <div class="product-name">UCSC T-Shirt</div>
                  <div class="product-category">Apparel</div>
                </div>
              </div>
              <div class="product-stats">
                <div class="product-sales">248 sold</div>
                <div class="product-revenue">Rs. 49,600</div>
              </div>
            </div>

            <div class="product-row">
              <div class="product-info">
                <img src="https://via.placeholder.com/48x48/10B981/ffffff?text=WB" alt="UCSC Wrist Band" class="product-image">
                <div class="product-details">
                  <div class="product-name">UCSC Wrist Band</div>
                  <div class="product-category">Accessories</div>
                </div>
              </div>
              <div class="product-stats">
                <div class="product-sales">182 sold</div>
                <div class="product-revenue">Rs. 18,200</div>
              </div>
            </div>

            <div class="product-row">
              <div class="product-info">
                <img src="https://via.placeholder.com/48x48/F59E0B/ffffff?text=J" alt="UOC Jersey" class="product-image">
                <div class="product-details">
                  <div class="product-name">UOC Jersey</div>
                  <div class="product-category">Apparel</div>
                </div>
              </div>
              <div class="product-stats">
                <div class="product-sales">156 sold</div>
                <div class="product-revenue">Rs. 31,200</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Minimal Admin Analytics JavaScript -->
<script src="/js/app/admin/marketplace/admin-analytics.js"></script>