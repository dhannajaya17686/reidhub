/**
 * Seller Portal Analytics Dashboard
 * ===============================
 * Enhanced analytics dashboard with Chart.js integration for comprehensive
 * sales performance tracking and customer insights visualization.
 */

class SellerAnalytics {
  constructor() {
    this.charts = {};
    this.analyticsData = {};
    this.init();
  }

  /**
   * Initialize the analytics dashboard
   */
  init() {
    this.loadAnalyticsData();
    this.setupEventListeners();
    this.initializeCharts();
    this.updateStatCards();
    this.setupDateRangeFilter();
    this.addAccessibilityFeatures();
  }

  /**
   * Load analytics data (simulated - replace with API calls)
   */
  loadAnalyticsData() {
    this.analyticsData = {
      totalSales: 20000,
      totalOrders: 20,
      totalCustomers: 10,
      averageOrderValue: 2000,
      customerSatisfaction: 4.0,
      salesTrend: [
        { date: '2024-01-01', sales: 1200, orders: 3 },
        { date: '2024-01-02', sales: 1500, orders: 4 },
        { date: '2024-01-03', sales: 1800, orders: 5 },
        { date: '2024-01-04', sales: 2200, orders: 6 },
        { date: '2024-01-05', sales: 1900, orders: 4 },
        { date: '2024-01-06', sales: 2500, orders: 7 },
        { date: '2024-01-07', sales: 2800, orders: 8 }
      ],
      topSellingItems: [
        { name: 'UCSC TShirt', sales: 48, revenue: 9600, flag: '🇺🇸' },
        { name: 'UCSC Wrist Band', sales: 12, revenue: 7200, flag: '🇬🇧' },
        { name: 'UOC Jersey', sales: 9, revenue: 16200, flag: '🇨🇭' }
      ],
      recentMessages: [
        { 
          user: 'Wrote Warren', 
          message: 'Meeting rescheduled', 
          time: '2:31 PM',
          avatar: 'https://via.placeholder.com/40x40/0466C8/ffffff?text=WW'
        },
        { 
          user: 'Jerry Wilson', 
          message: 'Update on remarketing campaign', 
          time: '2:28 PM',
          avatar: 'https://via.placeholder.com/40x40/10B981/ffffff?text=JW'
        },
        { 
          user: 'Robert Fox', 
          message: 'Sales launched by 3x', 
          time: '2:25 PM',
          avatar: 'https://via.placeholder.com/40x40/F59E0B/ffffff?text=RF'
        },
        { 
          user: 'Jane Cooper', 
          message: 'Some deadline news for the product launch', 
          time: '2:24 PM',
          avatar: 'https://via.placeholder.com/40x40/EF4444/ffffff?text=JC'
        }
      ]
    };
  }

  /**
   * Setup event listeners
   */
  setupEventListeners() {
    // Date range filter
    const dateFilter = document.getElementById('date-filter');
    if (dateFilter) {
      dateFilter.addEventListener('change', (e) => {
        this.handleDateRangeChange(e.target.value);
      });
    }

    // Export functionality
    const exportBtn = document.getElementById('export-analytics');
    if (exportBtn) {
      exportBtn.addEventListener('click', () => this.exportAnalytics());
    }

    // Refresh data
    const refreshBtn = document.getElementById('refresh-data');
    if (refreshBtn) {
      refreshBtn.addEventListener('click', () => this.refreshAnalytics());
    }
  }

  /**
   * Initialize all charts
   */
  initializeCharts() {
    this.createSalesChart();
    this.createOrdersChart();
    this.createCustomerSatisfactionChart();
    this.createTopProductsChart();
  }

  /**
   * Create sales trend chart
   */
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
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: '#0466C8',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
              label: function(context) {
                return `Sales: Rs. ${context.parsed.y.toLocaleString()}`;
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#64748B',
              font: {
                size: 12
              }
            }
          },
          y: {
            grid: {
              color: '#E2E8F0',
              drawBorder: false
            },
            ticks: {
              color: '#64748B',
              font: {
                size: 12
              },
              callback: function(value) {
                return 'Rs. ' + value.toLocaleString();
              }
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index'
        }
      }
    });
  }

  /**
   * Create orders trend chart
   */
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
          legend: {
            display: false
          },
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
          x: {
            grid: {
              display: false
            },
            ticks: {
              color: '#64748B',
              font: {
                size: 12
              }
            }
          },
          y: {
            grid: {
              color: '#E2E8F0',
              drawBorder: false
            },
            ticks: {
              color: '#64748B',
              font: {
                size: 12
              },
              stepSize: 1
            }
          }
        }
      }
    });
  }

  /**
   * Create customer satisfaction doughnut chart
   */
  createCustomerSatisfactionChart() {
    const ctx = document.getElementById('satisfaction-chart');
    if (!ctx) return;

    this.charts.satisfaction = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Excellent', 'Good', 'Average', 'Poor'],
        datasets: [{
          data: [65, 25, 8, 2],
          backgroundColor: [
            '#10B981',
            '#0466C8',
            '#F59E0B',
            '#EF4444'
          ],
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
            labels: {
              padding: 20,
              usePointStyle: true,
              font: {
                size: 12
              },
              color: '#64748B'
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            cornerRadius: 8,
            callbacks: {
              label: function(context) {
                return context.label + ': ' + context.parsed + '%';
              }
            }
          }
        }
      }
    });
  }

  /**
   * Create top products horizontal bar chart
   */
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
          backgroundColor: ['#0466C8', '#10B981', '#F59E0B'],
          borderRadius: 4,
          borderSkipped: false
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
              label: function(context) {
                return `Sales: ${context.parsed.x} units`;
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              color: '#E2E8F0',
              drawBorder: false
            },
            ticks: {
              color: '#64748B',
              font: {
                size: 12
              }
            }
          },
          y: {
            grid: {
              display: false
            },
            ticks: {
              color: '#64748B',
              font: {
                size: 12
              }
            }
          }
        }
      }
    });
  }

  /**
   * Update statistic cards with animations
   */
  updateStatCards() {
    const stats = [
      { id: 'total-sales', value: this.analyticsData.totalSales, prefix: 'Rs. ', suffix: '' },
      { id: 'total-orders', value: this.analyticsData.totalOrders, prefix: '', suffix: '' },
      { id: 'total-customers', value: this.analyticsData.totalCustomers, prefix: '', suffix: '' },
      { id: 'avg-order-value', value: this.analyticsData.averageOrderValue, prefix: 'Rs. ', suffix: '' },
      { id: 'customer-satisfaction', value: this.analyticsData.customerSatisfaction, prefix: '', suffix: '.0' }
    ];

    stats.forEach(stat => {
      const element = document.getElementById(stat.id);
      if (element) {
        this.animateNumber(element, 0, stat.value, stat.prefix, stat.suffix);
      }
    });
  }

  /**
   * Animate number counting effect
   */
  animateNumber(element, start, end, prefix = '', suffix = '') {
    const duration = 1000;
    const startTime = performance.now();

    const updateNumber = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const current = Math.floor(start + (end - start) * progress);
      
      element.textContent = prefix + current.toLocaleString() + suffix;

      if (progress < 1) {
        requestAnimationFrame(updateNumber);
      }
    };

    requestAnimationFrame(updateNumber);
  }

  /**
   * Setup date range filter
   */
  setupDateRangeFilter() {
    const filterButtons = document.querySelectorAll('.date-filter-btn');
    filterButtons.forEach(button => {
      button.addEventListener('click', (e) => {
        // Remove active class from all buttons
        filterButtons.forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        e.target.classList.add('active');
        
        const range = e.target.dataset.range;
        this.handleDateRangeChange(range);
      });
    });
  }

  /**
   * Handle date range change
   */
  handleDateRangeChange(range) {
    console.log('Date range changed to:', range);
    // Simulate data update based on range
    this.updateChartsForDateRange(range);
    this.announceToScreenReader(`Analytics updated for ${range} period`);
  }

  /**
   * Update charts for new date range
   */
  updateChartsForDateRange(range) {
    // Simulate different data based on range
    let multiplier = 1;
    switch(range) {
      case 'week':
        multiplier = 0.3;
        break;
      case 'month':
        multiplier = 1;
        break;
      case 'quarter':
        multiplier = 2.5;
        break;
      case 'year':
        multiplier = 10;
        break;
    }

    // Update sales chart
    if (this.charts.sales) {
      this.charts.sales.data.datasets[0].data = this.analyticsData.salesTrend.map(
        item => Math.floor(item.sales * multiplier)
      );
      this.charts.sales.update('active');
    }

    // Update orders chart
    if (this.charts.orders) {
      this.charts.orders.data.datasets[0].data = this.analyticsData.salesTrend.map(
        item => Math.floor(item.orders * multiplier * 0.8)
      );
      this.charts.orders.update('active');
    }
  }

  /**
   * Export analytics data
   */
  exportAnalytics() {
    const data = {
      exportDate: new Date().toISOString(),
      ...this.analyticsData
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `seller-analytics-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    this.showNotification('Analytics data exported successfully', 'success');
  }

  /**
   * Refresh analytics data
   */
  async refreshAnalytics() {
    const refreshBtn = document.getElementById('refresh-data');
    if (refreshBtn) {
      refreshBtn.disabled = true;
      refreshBtn.innerHTML = '<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/></svg> Refreshing...';
    }

    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500));

    // Reload data and update charts
    this.loadAnalyticsData();
    this.updateStatCards();
    Object.values(this.charts).forEach(chart => chart.update());

    if (refreshBtn) {
      refreshBtn.disabled = false;
      refreshBtn.innerHTML = 'Refresh Data';
    }

    this.showNotification('Analytics data refreshed', 'success');
  }

  /**
   * Add accessibility features
   */
  addAccessibilityFeatures() {
    // Add aria-labels to charts
    const chartContainers = document.querySelectorAll('.chart-container');
    chartContainers.forEach(container => {
      const canvas = container.querySelector('canvas');
      if (canvas) {
        canvas.setAttribute('role', 'img');
        canvas.setAttribute('aria-label', 'Analytics chart');
      }
    });

    // Create live region for announcements
    this.createLiveRegion();
  }

  /**
   * Create live region for screen reader announcements
   */
  createLiveRegion() {
    if (!document.getElementById('analytics-live-region')) {
      const liveRegion = document.createElement('div');
      liveRegion.id = 'analytics-live-region';
      liveRegion.setAttribute('aria-live', 'polite');
      liveRegion.setAttribute('aria-atomic', 'true');
      liveRegion.style.cssText = `
        position: absolute;
        left: -10000px;
        width: 1px;
        height: 1px;
        overflow: hidden;
      `;
      document.body.appendChild(liveRegion);
    }
  }

  /**
   * Announce changes to screen readers
   */
  announceToScreenReader(message) {
    const liveRegion = document.getElementById('analytics-live-region');
    if (liveRegion) {
      liveRegion.textContent = message;
    }
  }

  /**
   * Show notification
   */
  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification--${type}`;
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === 'success' ? '#10B981' : type === 'error' ? '#EF4444' : '#0466C8'};
      color: white;
      padding: 16px 20px;
      border-radius: 8px;
      font-weight: 500;
      font-size: 14px;
      z-index: 1000;
      max-width: 400px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      animation: slideInRight 0.3s ease-out;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
      notification.style.animation = 'slideOutRight 0.3s ease-in forwards';
      setTimeout(() => {
        if (notification.parentElement) {
          notification.parentElement.removeChild(notification);
        }
      }, 300);
    }, 3000);
  }
}

// Animation styles
const animationStyles = `
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }

  .animate-spin {
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
`;

// Add animation styles to document
const styleSheet = document.createElement('style');
styleSheet.textContent = animationStyles;
document.head.appendChild(styleSheet);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  new SellerAnalytics();
});

// Export for potential module usage
export { SellerAnalytics };