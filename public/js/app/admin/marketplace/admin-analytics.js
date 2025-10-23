/**
 * Admin Analytics Dashboard - Minimal JavaScript
 */
class AdminAnalytics {
  constructor() {
    this.charts = {};
    this.data = {
      revenue: [8500, 12000, 15500, 18200, 22000, 25500, 28000],
      orders: [45, 62, 78, 85, 92, 108, 115],
      labels: ['Oct 16', 'Oct 17', 'Oct 18', 'Oct 19', 'Oct 20', 'Oct 21', 'Oct 22'],
      categories: [35, 25, 20, 15, 5],
      sellers: [45, 32, 28, 22, 18]
    };
    this.init();
  }

  init() {
    if (typeof Chart === 'undefined') {
      console.error('Chart.js not loaded');
      return;
    }
    this.createCharts();
    this.setupFilters();
  }

  createCharts() {
    this.createRevenueChart();
    this.createOrdersChart();
    this.createCategoriesChart();
    this.createSellersChart();
  }

  createRevenueChart() {
    const ctx = document.getElementById('revenue-chart');
    if (!ctx) return;

    this.charts.revenue = new Chart(ctx, {
      type: 'line',
      data: {
        labels: this.data.labels,
        datasets: [{
          data: this.data.revenue,
          borderColor: '#10B981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { 
            grid: { color: '#E2E8F0' },
            ticks: { callback: value => 'Rs. ' + value }
          }
        }
      }
    });
  }

  createOrdersChart() {
    const ctx = document.getElementById('orders-chart');
    if (!ctx) return;

    this.charts.orders = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: this.data.labels,
        datasets: [{
          data: this.data.orders,
          backgroundColor: '#0466C8',
          borderRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { color: '#E2E8F0' } }
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
        labels: ['Apparel', 'Accessories', 'Books', 'Electronics', 'Other'],
        datasets: [{
          data: this.data.categories,
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
            labels: { usePointStyle: true, padding: 15 }
          }
        }
      }
    });
  }

  createSellersChart() {
    const ctx = document.getElementById('sellers-chart');
    if (!ctx) return;

    this.charts.sellers = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Alice Cooper', 'Bob Smith', 'Carol Davis', 'David Wilson', 'Eva Brown'],
        datasets: [{
          data: this.data.sellers,
          backgroundColor: ['#0466C8', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
          borderRadius: 4
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: '#E2E8F0' } },
          y: { grid: { display: false } }
        }
      }
    });
  }

  setupFilters() {
    document.querySelectorAll('.date-filter-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        document.querySelectorAll('.date-filter-btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        
        const range = e.target.dataset.range;
        this.updateCharts(range);
      });
    });

    const refreshBtn = document.getElementById('refresh-data');
    if (refreshBtn) {
      refreshBtn.addEventListener('click', () => this.refreshData());
    }

    const exportBtn = document.getElementById('export-data');
    if (exportBtn) {
      exportBtn.addEventListener('click', () => this.exportData());
    }
  }

  updateCharts(range) {
    const multiplier = range === 'week' ? 0.3 : range === 'month' ? 1 : range === 'quarter' ? 2.5 : 10;
    
    if (this.charts.revenue) {
      this.charts.revenue.data.datasets[0].data = this.data.revenue.map(v => Math.floor(v * multiplier));
      this.charts.revenue.update();
    }
    
    if (this.charts.orders) {
      this.charts.orders.data.datasets[0].data = this.data.orders.map(v => Math.floor(v * multiplier * 0.8));
      this.charts.orders.update();
    }
  }

  refreshData() {
    const btn = document.getElementById('refresh-data');
    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Refreshing...';
      
      setTimeout(() => {
        btn.disabled = false;
        btn.textContent = 'Refresh';
        Object.values(this.charts).forEach(chart => chart.update());
      }, 1000);
    }
  }

  exportData() {
    const data = {
      date: new Date().toISOString(),
      revenue: this.data.revenue,
      orders: this.data.orders
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'admin-analytics.json';
    a.click();
    URL.revokeObjectURL(url);
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  new AdminAnalytics();
});