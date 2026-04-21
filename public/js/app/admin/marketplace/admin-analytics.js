/**
 * Admin Analytics Dashboard
 * Platform-wide marketplace analytics with real data from API
 */
class AdminAnalytics {
  constructor() {
    this.charts = {};
    this.analyticsData = null;
    this.range = '30d';
    this.init();
  }

  async init() {
    await this.fetchAnalytics(this.range);      // load real data
    this.setupEventListeners();
    this.initializeCharts();                    // build charts from real data
    this.updateStatCards();                     // hydrate stat cards
    this.populateRecentOrders();                // populate recent orders table
    this.populateTopProducts();                 // populate top products list
    this.setupDateRangeFilter();
  }

  async fetchAnalytics(range = '30d') {
    try {
      const res = await fetch(`/dashboard/marketplace/admin/analytics/data?range=${encodeURIComponent(range)}`);
      const data = await res.json();
      if (!res.ok || !data?.success) throw new Error(data?.message || 'Failed to load analytics');

      // Map API -> view model
      const k = data.kpis || {};
      const s = data.series || { labels: [], revenue: [], orders: [], units: [] };
      const tp = data.topProducts || { labels: [], units: [], revenue: [] };
      const tc = data.topCategories || { labels: [], itemCount: [] };
      const ro = data.recentOrders || [];

      // Revenue Trend array
      const revenueTrend = (s.labels || []).map((d, i) => ({
        date: d,
        revenue: Number(s.revenue?.[i] || 0),
        orders: Number(s.orders?.[i] || 0),
        units: Number(s.units?.[i] || 0)
      }));

      // Top selling products
      const topSellingProducts = (tp.labels || []).map((name, i) => ({
        name,
        productImage: tp.images?.[i] || '/images/placeholders/product.png',
        units: Number(tp.units?.[i] || 0),
        revenue: Number(tp.revenue?.[i] || 0)
      }));

      // Item type breakdown (new vs used)
      const categoryBreakdown = (tc.labels || []).map((type, i) => ({
        type,
        count: Number(tc.itemCount?.[i] || 0)
      }));

      this.analyticsData = {
        totalRevenue: Number(k.revenue || 0),
        totalOrders: Number(k.orders || 0),
        totalUnits: Number(k.units || 0),
        totalCustomers: Number(k.customers || 0),
        activeSellers: Number(k.activeSellers || 0),
        revenueTrend,
        topSellingProducts,
        categoryBreakdown,
        recentOrders: ro
      };
    } catch (e) {
      console.error('Analytics fetch failed:', e);
      this.analyticsData = {
        totalRevenue: 0, totalOrders: 0, totalUnits: 0, totalCustomers: 0, activeSellers: 0,
        revenueTrend: [],
        topSellingProducts: [],
        categoryBreakdown: [],
        recentOrders: []
      };
    }
  }

  setupEventListeners() {
    // Refresh - reload page
    const refreshBtn = document.getElementById('refresh-data');
    if (refreshBtn) {
      refreshBtn.addEventListener('click', () => {
        location.reload();
      });
    }

    // Export
    const exportBtn = document.getElementById('export-data');
    if (exportBtn) {
      exportBtn.addEventListener('click', () => this.exportAnalytics());
    }
  }

  initializeCharts() {
    this.createRevenueChart();
    this.createOrdersChart();
    this.createCategoriesChart();
  }

  redraw() {
    this.updateStatCards();
    this.updateRevenueChart();
    this.updateOrdersChart();
    this.updateCategoriesChart();
    this.populateRecentOrders();
    this.populateTopProducts();
  }

  setupDateRangeFilter() {
    document.querySelectorAll('.date-filter-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        // Update active state
        document.querySelectorAll('.date-filter-btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');

        // Fetch new data
        const range = e.target.dataset.range || '30d';
        this.range = range;
        await this.fetchAnalytics(this.range);
        this.redraw();
      });
    });
  }

  createRevenueChart() {
    const ctx = document.getElementById('revenue-chart');
    if (!ctx) return;

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');

    this.charts.revenue = new Chart(ctx, {
      type: 'line',
      data: {
        labels: this.analyticsData.revenueTrend.map(item =>
          new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
        ),
        datasets: [{
          label: 'Revenue (Rs.)',
          data: this.analyticsData.revenueTrend.map(item => item.revenue),
          borderColor: '#10B981',
          backgroundColor: gradient,
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#10B981',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 2,
          pointRadius: 6,
          pointHoverRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#10B981',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
              label: (ctx) => `Revenue: Rs. ${Number(ctx.parsed.y).toLocaleString()}`
            }
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#64748B', font: { size: 12 } } },
          y: {
            grid: { color: '#E2E8F0', drawBorder: false },
            ticks: {
              color: '#64748B', font: { size: 12 },
              callback: (v) => 'Rs. ' + Number(v).toLocaleString()
            }
          }
        },
        interaction: { intersect: false, mode: 'index' }
      }
    });
  }

  createOrdersChart() {
    const ctx = document.getElementById('orders-chart');
    if (!ctx) return;

    this.charts.orders = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: this.analyticsData.revenueTrend.map(item =>
          new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
        ),
        datasets: [{
          label: 'Orders',
          data: this.analyticsData.revenueTrend.map(item => item.orders),
          backgroundColor: '#0466C8',
          borderColor: '#0466C8',
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#0466C8',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#64748B', font: { size: 12 } } },
          y: { grid: { color: '#E2E8F0', drawBorder: false }, ticks: { color: '#64748B', font: { size: 12 } } }
        }
      }
    });
  }

  createCategoriesChart() {
    const ctx = document.getElementById('categories-chart');
    if (!ctx) return;

    this.charts.categories = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: this.analyticsData.categoryBreakdown.map(item => item.type),
        datasets: [{
          data: this.analyticsData.categoryBreakdown.map(item => item.count),
          backgroundColor: ['#0466C8', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
          borderWidth: 0,
          cutout: '60%'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { padding: 20, usePointStyle: true, font: { size: 12 }, color: '#64748B' }
          }
        }
      }
    });
  }

  updateRevenueChart() {
    if (!this.charts.revenue) return;
    this.charts.revenue.data.labels = this.analyticsData.revenueTrend.map(item =>
      new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    );
    this.charts.revenue.data.datasets[0].data = this.analyticsData.revenueTrend.map(item => item.revenue);
    this.charts.revenue.update('active');
  }

  updateOrdersChart() {
    if (!this.charts.orders) return;
    this.charts.orders.data.labels = this.analyticsData.revenueTrend.map(item =>
      new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    );
    this.charts.orders.data.datasets[0].data = this.analyticsData.revenueTrend.map(item => item.orders);
    this.charts.orders.update('active');
  }

  updateCategoriesChart() {
    if (!this.charts.categories) return;
    this.charts.categories.data.labels = this.analyticsData.categoryBreakdown.map(item => item.type);
    this.charts.categories.data.datasets[0].data = this.analyticsData.categoryBreakdown.map(item => item.count);
    this.charts.categories.update('active');
  }

  updateStatCards() {
    if (!this.analyticsData) return;

    const stats = [
      { id: 'total-revenue', value: this.analyticsData.totalRevenue, format: 'currency' },
      { id: 'total-orders', value: this.analyticsData.totalOrders, format: 'number' },
      { id: 'active-sellers', value: this.analyticsData.activeSellers, format: 'number' }
    ];

    stats.forEach(stat => {
      const el = document.getElementById(stat.id);
      if (!el) return;
      if (stat.format === 'currency') {
        el.textContent = 'Rs. ' + Number(stat.value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      } else {
        el.textContent = Number(stat.value).toLocaleString();
      }
    });
  }

  populateRecentOrders() {
    if (!this.analyticsData?.recentOrders) return;

    const tableBody = document.querySelector('[data-table="recent-orders"] tbody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    this.analyticsData.recentOrders.forEach(order => {
      const row = document.createElement('tr');
      const statusClass = this.getStatusClass(order.status);
      const date = new Date(order.date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

      row.innerHTML = `
        <td><span class="order-id">#ORD-${String(order.id).padStart(6, '0')}</span></td>
        <td>
          <div class="customer-info">
            <img src="${order.productImage || '/images/placeholders/product.png'}" alt="${order.productTitle || 'Product'}" class="customer-avatar" onerror="this.src='/images/placeholders/product.png'">
            <span>${order.customerName}</span>
          </div>
        </td>
        <td><strong>Rs. ${Number(order.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
        <td><span class="status ${statusClass}">${this.formatStatus(order.status)}</span></td>
        <td>${date}</td>
      `;
      tableBody.appendChild(row);
    });
  }

  populateTopProducts() {
    if (!this.analyticsData?.topSellingProducts) return;

    const container = document.querySelector('.products-list');
    if (!container) return;

    container.innerHTML = '';

    this.analyticsData.topSellingProducts.forEach(product => {
      const row = document.createElement('div');
      row.className = 'product-row';
      const initials = product.name.substring(0, 2).toUpperCase();

      row.innerHTML = `
        <div class="product-info">
          <img src="${product.productImage || '/images/placeholders/product.png'}" alt="${product.name}" class="product-image" onerror="this.src='/images/placeholders/product.png'">
          <div class="product-details">
            <div class="product-name">${product.name}</div>
            <div class="product-category">Product</div>
          </div>
        </div>
        <div class="product-stats">
          <div class="product-sales">${product.units} sold</div>
          <div class="product-revenue">Rs. ${Number(product.revenue).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}</div>
        </div>
      `;
      container.appendChild(row);
    });
  }

  getStatusClass(status) {
    const statusMap = {
      'delivered': 'status--completed',
      'pending': 'status--pending',
      'yet_to_ship': 'status--pending',
      'processing': 'status--processing',
      'shipped': 'status--processing',
      'cancelled': 'status--cancelled',
    };
    return statusMap[status] || 'status--pending';
  }

  formatStatus(status) {
    const statusMap = {
      'delivered': 'Delivered',
      'pending': 'Pending',
      'yet_to_ship': 'Yet to Ship',
      'processing': 'Processing',
      'shipped': 'Shipped',
      'cancelled': 'Cancelled',
    };
    return statusMap[status] || status;
  }

  getInitials(name) {
    return name.split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 2);
  }

  exportAnalytics() {
    const data = {
      date: new Date().toISOString(),
      range: this.range,
      kpis: {
        totalRevenue: this.analyticsData.totalRevenue,
        totalOrders: this.analyticsData.totalOrders,
        totalCustomers: this.analyticsData.totalCustomers,
        activeSellers: this.analyticsData.activeSellers
      }
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `admin-analytics-${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    URL.revokeObjectURL(url);
  }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new AdminAnalytics();
});