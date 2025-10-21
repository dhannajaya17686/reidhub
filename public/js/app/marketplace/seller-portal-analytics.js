/**
 * Seller Portal Analytics Dashboard
 * Replace dummy data with live API data while keeping layout/styles intact.
 */
class SellerAnalytics {
  constructor() {
    this.charts = {};
    this.analyticsData = null;
    this.range = '30d';
    this.init();
  }

  async init() {
    await this.fetchAnalytics(this.range);   // load real data
    this.setupEventListeners();
    this.initializeCharts();                 // build charts from real data
    this.updateStatCards();                  // hydrate stat cards
    this.populateTopItemsList();             // replace dummy list with real top products
    this.setupDateRangeFilter();
    this.addAccessibilityFeatures();
  }

  async fetchAnalytics(range = '30d') {
    try {
      const res = await fetch(`/dashboard/marketplace/seller/analytics/data?range=${encodeURIComponent(range)}`);
      const data = await res.json();
      if (!res.ok || !data?.success) throw new Error(data?.message || 'Failed to load analytics');

      // Map API -> view model used by existing charts/cards
      const k = data.kpis || {};
      const s = data.series || { labels: [], revenue: [], orders: [], units: [] };
      const tp = data.topProducts || { labels: [], units: [], revenue: [] };

      // Compose salesTrend array the charts expect
      const salesTrend = (s.labels || []).map((d, i) => ({
        date: d,
        sales: Number(s.revenue?.[i] || 0),
        orders: Number(s.orders?.[i] || 0)
      }));

      // Build top selling items for the list and products chart
      const topSellingItems = (tp.labels || []).map((name, i) => ({
        name,
        sales: Number(tp.units?.[i] || 0),
        revenue: Number(tp.revenue?.[i] || 0),
        flag: '' // keep layout; no flag data from backend
      }));

      this.analyticsData = {
        totalSales: Number(k.revenue || 0),
        totalOrders: Number(k.orders || 0),
        totalCustomers: Number(k.customers || 0),
        averageOrderValue: Number(k.aov || 0),
        // satisfaction not available in API; keep as-is
        customerSatisfaction: 4.0,
        salesTrend,
        topSellingItems,
        // keep messages section as-is (static in view)
      };
    } catch (e) {
      console.error('Analytics fetch failed:', e);
      // Fallback to empty datasets to avoid breaking UI
      this.analyticsData = {
        totalSales: 0, totalOrders: 0, totalCustomers: 0, averageOrderValue: 0,
        customerSatisfaction: 4.0,
        salesTrend: [],
        topSellingItems: []
      };
    }
  }

  setupEventListeners() {
    // Export
    const exportBtn = document.getElementById('export-analytics');
    exportBtn?.addEventListener('click', () => this.exportAnalytics());

    // Refresh
    const refreshBtn = document.getElementById('refresh-data');
    refreshBtn?.addEventListener('click', async () => {
      await this.refreshAnalytics();
    });

    // Date filter (if present)
    const dateFilter = document.getElementById('date-filter');
    dateFilter?.addEventListener('change', async (e) => {
      this.range = e.target.value || '30d';
      await this.fetchAnalytics(this.range);
      this.redraw();
    });
  }

  initializeCharts() {
    this.createSalesChart();
    this.createOrdersChart();
    this.createCustomerSatisfactionChart(); // remains static (no API yet)
    this.createTopProductsChart();
  }

  redraw() {
    // Update cards
    this.updateStatCards();
    // Update charts
    if (this.charts.sales) {
      this.charts.sales.data.labels = this.analyticsData.salesTrend.map(i =>
        new Date(i.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
      );
      this.charts.sales.data.datasets[0].data = this.analyticsData.salesTrend.map(i => i.sales);
      this.charts.sales.update('active');
    }
    if (this.charts.orders) {
      this.charts.orders.data.labels = this.analyticsData.salesTrend.map(i =>
        new Date(i.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
      );
      this.charts.orders.data.datasets[0].data = this.analyticsData.salesTrend.map(i => i.orders);
      this.charts.orders.update('active');
    }
    if (this.charts.products) {
      this.charts.products.data.labels = this.analyticsData.topSellingItems.map(i => i.name);
      this.charts.products.data.datasets[0].data = this.analyticsData.topSellingItems.map(i => i.sales);
      this.charts.products.update('active');
    }
    this.populateTopItemsList();
  }

  createSalesChart() {
    const ctx = document.getElementById('sales-chart');
    if (!ctx) return;

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(4, 102, 200, 0.2)');
    gradient.addColorStop(1, 'rgba(4, 102, 200, 0.02)');

    this.charts.sales = new Chart(ctx, {
      type: 'line',
      data: {
        labels: this.analyticsData.salesTrend.map(item =>
          new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
        ),
        datasets: [{
          label: 'Sales (Rs.)',
          data: this.analyticsData.salesTrend.map(item => item.sales),
          borderColor: '#0466C8',
          backgroundColor: gradient,
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#0466C8',
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
            borderColor: '#0466C8',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
              label: (ctx) => `Sales: Rs. ${Number(ctx.parsed.y).toLocaleString()}`
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
        labels: this.analyticsData.salesTrend.map(item =>
          new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
        ),
        datasets: [{
          label: 'Orders',
          data: this.analyticsData.salesTrend.map(item => item.orders),
          backgroundColor: '#10B981',
          borderColor: '#10B981',
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
            borderColor: '#10B981',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#64748B', font: { size: 12 } } },
          y: { grid: { color: '#E2E8F0', drawBorder: false }, ticks: { color: '#64748B', font: { size: 12 }, stepSize: 1 } }
        }
      }
    });
  }

  // Satisfaction chart remains static (no ratings API available yet)
  createCustomerSatisfactionChart() {
    const ctx = document.getElementById('satisfaction-chart');
    if (!ctx) return;

    this.charts.satisfaction = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Excellent', 'Good', 'Average', 'Poor'],
        datasets: [{
          data: [65, 25, 8, 2],
          backgroundColor: ['#10B981','#0466C8','#F59E0B','#EF4444'],
          borderWidth: 0,
          cutout: '70%'
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

  createTopProductsChart() {
    const ctx = document.getElementById('products-chart');
    if (!ctx) return;

    this.charts.products = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: this.analyticsData.topSellingItems.map(item => item.name),
        datasets: [{
          label: 'Sales',
          data: this.analyticsData.topSellingItems.map(item => item.sales),
          backgroundColor: ['#0466C8', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
          borderRadius: 4,
          borderSkipped: false
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            cornerRadius: 8,
            displayColors: false,
            callbacks: { label: (ctx) => `Sales: ${ctx.parsed.x} units` }
          }
        },
        scales: {
          x: { grid: { color: '#E2E8F0', drawBorder: false }, ticks: { color: '#64748B', font: { size: 12 } } },
          y: { grid: { display: false }, ticks: { color: '#64748B', font: { size: 12 } } }
        }
      }
    });
  }

  updateStatCards() {
    if (!this.analyticsData) return;
    const stats = [
      { id: 'total-sales', value: this.analyticsData.totalSales, prefix: 'Rs. ' },
      { id: 'total-orders', value: this.analyticsData.totalOrders },
      { id: 'total-customers', value: this.analyticsData.totalCustomers },
      { id: 'avg-order-value', value: this.analyticsData.averageOrderValue, prefix: 'Rs. ' },
    ];
    stats.forEach(stat => {
      const el = document.getElementById(stat.id);
      if (el) el.textContent = `${stat.prefix || ''}${Number(stat.value || 0).toLocaleString()}`;
    });
  }

  populateTopItemsList() {
    const container = document.querySelector('.items-list');
    if (!container || !this.analyticsData) return;
    const items = this.analyticsData.topSellingItems;
    if (!items.length) { container.innerHTML = '<div class="item-row">No data</div>'; return; }

    container.innerHTML = items.map(it => `
      <div class="item-row">
        <div class="item-info">
          <span class="item-flag">${it.flag || ''}</span>
          <div class="item-details">
            <div class="item-name">${this.esc(it.name)}</div>
            <div class="item-category"></div>
          </div>
        </div>
        <div class="item-stats">
          <div class="item-sales">${Number(it.sales).toLocaleString()}</div>
          <div class="item-revenue">Rs. ${Number(it.revenue).toLocaleString()}</div>
        </div>
      </div>
    `).join('');
  }

  setupDateRangeFilter() {
    const filterButtons = document.querySelectorAll('.date-filter-btn');
    filterButtons.forEach(btn => {
      btn.addEventListener('click', async (e) => {
        filterButtons.forEach(b => b.classList.remove('active'));
        e.currentTarget.classList.add('active');
        const range = e.currentTarget.dataset.range || '30d';
        this.range = range;
        await this.fetchAnalytics(range);
        this.redraw();
        this.announceToScreenReader(`Analytics updated for ${range} period`);
      });
    });
  }

  exportAnalytics() {
    const data = {
      exportDate: new Date().toISOString(),
      kpis: {
        totalSales: this.analyticsData.totalSales,
        totalOrders: this.analyticsData.totalOrders,
        totalCustomers: this.analyticsData.totalCustomers,
        averageOrderValue: this.analyticsData.averageOrderValue,
      },
      salesTrend: this.analyticsData.salesTrend,
      topSellingItems: this.analyticsData.topSellingItems
    };
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `seller-analytics-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    URL.revokeObjectURL(url);
    this.showNotification('Analytics data exported successfully', 'success');
  }

  async refreshAnalytics() {
    const btn = document.getElementById('refresh-data');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/></svg> Refreshing...';
    }
    await this.fetchAnalytics(this.range);
    this.redraw();
    if (btn) { btn.disabled = false; btn.innerHTML = 'Refresh Data'; }
    this.showNotification('Analytics data refreshed', 'success');
  }

  addAccessibilityFeatures() {
    const chartContainers = document.querySelectorAll('.chart-container');
    chartContainers.forEach(container => {
      const canvas = container.querySelector('canvas');
      if (canvas) {
        canvas.setAttribute('role', 'img');
        canvas.setAttribute('aria-label', 'Analytics chart');
      }
    });
    this.createLiveRegion();
  }

  createLiveRegion() {
    if (!document.getElementById('analytics-live-region')) {
      const liveRegion = document.createElement('div');
      liveRegion.id = 'analytics-live-region';
      liveRegion.setAttribute('aria-live', 'polite');
      liveRegion.setAttribute('aria-atomic', 'true');
      liveRegion.style.cssText = 'position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden;';
      document.body.appendChild(liveRegion);
    }
  }

  announceToScreenReader(message) {
    const liveRegion = document.getElementById('analytics-live-region');
    if (liveRegion) liveRegion.textContent = message;
  }

  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification--${type}`;
    notification.style.cssText = `
      position: fixed; top: 20px; right: 20px; background: ${type === 'success' ? '#10B981' : type === 'error' ? '#EF4444' : '#0466C8'};
      color: white; padding: 16px 20px; border-radius: 8px; font-weight: 500; font-size: 14px; z-index: 1000; max-width: 400px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1); animation: slideInRight 0.3s ease-out;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.3s ease-in forwards';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  esc(s) { return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
}

// Animation styles (unchanged)
const animationStyles = `
  @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
  @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
  .animate-spin { animation: spin 1s linear infinite; }
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
`;
const styleSheet = document.createElement('style');
styleSheet.textContent = animationStyles;
document.head.appendChild(styleSheet);

// Initialize
document.addEventListener('DOMContentLoaded', () => { new SellerAnalytics(); });

// Export (optional)
export { SellerAnalytics };