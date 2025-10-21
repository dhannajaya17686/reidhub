<link rel="stylesheet" href="/css/app/user/marketplace/seller-portal-analytics.css">
<!-- Chart.js Library - Make sure this loads BEFORE your custom script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script><!-- Your custom script AFTER Chart.js -->
<script type="module" src="/js/app/marketplace/seller-portal-analytics.js"></script>

<!-- Main Analytics Dashboard -->
<main class="analytics-main" role="main" aria-label="Seller Analytics Dashboard">
  
  <!-- Page Header -->
  <div class="page-header">
    <div class="header-content">
      <h1 class="page-title">Analytics</h1>
      <p class="page-subtitle">Track your sales performance and customer insights.</p>
    </div>
    
    <!-- Header Actions -->
    <div class="header-actions">
      <div class="action-buttons">
        <button class="btn btn--secondary" id="refresh-data">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 3v5h-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M3 21v-5h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Refresh Data
        </button>
        <button class="btn btn--primary" id="export-analytics">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Export
        </button>
      </div>
    </div>
  </div>

  <!-- Statistics Overview -->
  <section class="stats-overview" aria-label="Key Performance Metrics">
    <div class="stats-grid">
      <!-- Total Sales -->
      <div class="stat-card stat-card--primary">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M7 18a2 2 0 1 0 2 2 2 2 0 0 0-2-2Zm10 0a2 2 0 1 0 2 2 2 2 0 0 0-2-2ZM3 4h2l2.7 9.4A2 2 0 0 0 9.6 14h6.9a2 2 0 0 0 1.9-1.5L21 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Total Sales</div>
          <div class="stat-value" id="total-sales">Rs. 20,000</div>
          <div class="stat-change stat-change--positive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <polyline points="23,6 13.5,15.5 8.5,10.5 1,18" stroke="currentColor" stroke-width="2" fill="none"/>
              <polyline points="17,6 23,6 23,12" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            +12.5%
          </div>
        </div>
      </div>

      <!-- Total Orders -->
      <div class="stat-card stat-card--success">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="8" y="2" width="8" height="4" rx="1" ry="1" stroke="currentColor" stroke-width="2" fill="none"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Total Orders</div>
          <div class="stat-value" id="total-orders">20</div>
          <div class="stat-change stat-change--positive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <polyline points="23,6 13.5,15.5 8.5,10.5 1,18" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            +8.2%
          </div>
        </div>
      </div>

      <!-- Total Customers -->
      <div class="stat-card stat-card--info">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" fill="none"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Total Customers</div>
          <div class="stat-value" id="total-customers">10</div>
          <div class="stat-change stat-change--positive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <polyline points="23,6 13.5,15.5 8.5,10.5 1,18" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            +15.3%
          </div>
        </div>
      </div>

      <!-- Average Order Value -->
      <div class="stat-card stat-card--warning">
        <div class="stat-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-label">Average Order Value</div>
          <div class="stat-value" id="avg-order-value">Rs. 2,000</div>
          <div class="stat-change stat-change--positive">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <polyline points="23,6 13.5,15.5 8.5,10.5 1,18" stroke="currentColor" stroke-width="2" fill="none"/>
            </svg>
            +5.7%
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Charts Section -->
  <section class="charts-section">
    <!-- Sales & Orders Charts -->
    <div class="charts-row">
      <!-- Sales Trend Chart -->
      <div class="chart-card chart-card--large">
        <div class="chart-header">
          <h3 class="chart-title">Sales Trend</h3>
          <div class="chart-legend">
            <span class="legend-item">
              <span class="legend-color" style="background: #0466C8;"></span>
              Sales (Rs.)
            </span>
          </div>
        </div>
        <div class="chart-container">
          <canvas id="sales-chart" aria-label="Sales trend over time"></canvas>
        </div>
      </div>

      <!-- Orders Chart -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Orders Overview</h3>
        </div>
        <div class="chart-container">
          <canvas id="orders-chart" aria-label="Orders count over time"></canvas>
        </div>
      </div>
    </div>

    <!-- Customer Satisfaction & Top Products -->
    <div class="charts-row">
      <!-- Customer Satisfaction Chart -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Customer Satisfaction</h3>
        </div>
        <div class="chart-container">
          <canvas id="satisfaction-chart" aria-label="Customer satisfaction breakdown"></canvas>
        </div>
      </div>

      <!-- Top Products Chart -->
      <div class="chart-card">
        <div class="chart-header">
          <h3 class="chart-title">Top Selling Products</h3>
        </div>
        <div class="chart-container">
          <canvas id="products-chart" aria-label="Top selling products comparison"></canvas>
        </div>
      </div>
    </div>
  </section>

  <!-- Data Tables Section -->
  <section class="data-section">
    <div class="data-row">
      <!-- Top Selling Items -->
      <div class="data-card">
        <div class="data-header">
          <h3 class="data-title">Top Selling Items</h3>
          <button class="btn btn--small btn--outline">View All</button>
        </div>
        <div class="data-content">
          <div class="items-list">
            <div class="item-row">
              <div class="item-info">
                <span class="item-flag">🇺🇸</span>
                <div class="item-details">
                  <div class="item-name">UCSC TShirt</div>
                  <div class="item-category">Apparel</div>
                </div>
              </div>
              <div class="item-stats">
                <div class="item-sales">48</div>
                <div class="item-revenue">Rs. 9,600</div>
              </div>
            </div>

            <div class="item-row">
              <div class="item-info">
                <span class="item-flag">🇬🇧</span>
                <div class="item-details">
                  <div class="item-name">UCSC Wrist Band</div>
                  <div class="item-category">Accessories</div>
                </div>
              </div>
              <div class="item-stats">
                <div class="item-sales">12</div>
                <div class="item-revenue">Rs. 7,200</div>
              </div>
            </div>

            <div class="item-row">
              <div class="item-info">
                <span class="item-flag">🇨🇭</span>
                <div class="item-details">
                  <div class="item-name">UOC Jersey</div>
                  <div class="item-category">Apparel</div>
                </div>
              </div>
              <div class="item-stats">
                <div class="item-sales">9</div>
                <div class="item-revenue">Rs. 16,200</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Messages -->
      <div class="data-card">
        <div class="data-header">
          <h3 class="data-title">Recent Messages</h3>
          <button class="btn btn--small btn--outline">View All</button>
        </div>
        <div class="data-content">
          <div class="messages-list">
            <div class="message-row">
              <div class="message-avatar">
                <img src="/assets/placeholders/profile.png" alt="Wrote Warren">
              </div>
              <div class="message-content">
                <div class="message-header">
                  <div class="message-user">Wrote Warren</div>
                  <div class="message-time">2:31 PM</div>
                </div>
                <div class="message-text">Meeting rescheduled</div>
              </div>
            </div>

            <div class="message-row">
              <div class="message-avatar">
                <img src="/assets/placeholders/profile.png" alt="Jerry Wilson">
              </div>
              <div class="message-content">
                <div class="message-header">
                  <div class="message-user">Jerry Wilson</div>
                  <div class="message-time">2:28 PM</div>
                </div>
                <div class="message-text">Update on remarketing campaign</div>
              </div>
            </div>

            <div class="message-row">
              <div class="message-avatar">
                <img src="/assets/placeholders/profile.png" alt="Robert Fox">
              </div>
              <div class="message-content">
                <div class="message-header">
                  <div class="message-user">Robert Fox</div>
                  <div class="message-time">2:25 PM</div>
                </div>
                <div class="message-text">Sales launched by 3x</div>
              </div>
            </div>

            <div class="message-row">
              <div class="message-avatar">
                <img src="/assets/placeholders/profile.png" alt="Jane Cooper">
              </div>
              <div class="message-content">
                <div class="message-header">
                  <div class="message-user">Jane Cooper</div>
                  <div class="message-time">2:24 PM</div>
                </div>
                <div class="message-text">Some deadline news for the product launch</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>


