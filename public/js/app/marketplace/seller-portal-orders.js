/**
 * Orders Management - Enhanced with Status Management
 */

class OrdersManager {
  constructor() {
    this.currentTab = 'all';
    this.orders = [];
    this.filteredOrders = [];
    this.currentOrder = null;
    this.init();
  }

  init() {
    this.setupTabs();
    this.setupFilters();
    this.setupChat();
    this.loadOrders();
  }

  // Setup tab switching (updated for new tabs)
  setupTabs() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.currentTab = btn.dataset.status;
        this.filterOrders();
      });
    });
  }

  // Setup search and filters (updated for new statuses)
  setupFilters() {
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const dateFilter = document.getElementById('date-filter');

    searchInput.addEventListener('input', () => this.filterOrders());
    statusFilter.addEventListener('change', () => this.filterOrders());
    dateFilter.addEventListener('change', () => this.filterOrders());
  }

  // Setup chat functionality
  setupChat() {
    const messageInput = document.getElementById('message-input');
    
    messageInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        this.sendMessage();
      }
    });
  }

  // Load orders from DOM (updated for new structure)
  loadOrders() {
    const rows = document.querySelectorAll('.order-row');
    this.orders = Array.from(rows).map(row => ({
      element: row,
      id: row.querySelector('.order-id').textContent,
      item: row.querySelector('.item-name').textContent,
      user: row.querySelector('.user-name').textContent,
      date: row.querySelector('.date-placed').textContent,
      status: row.dataset.status,
      payment: row.dataset.payment
    }));
    
    this.filteredOrders = [...this.orders];
    this.updateDisplay();
  }

  // Filter orders (same logic)
  filterOrders() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const statusFilter = document.getElementById('status-filter').value;
    const dateFilter = document.getElementById('date-filter').value;

    this.filteredOrders = this.orders.filter(order => {
      if (this.currentTab !== 'all' && order.status !== this.currentTab) {
        return false;
      }

      if (searchTerm && !order.id.toLowerCase().includes(searchTerm) && 
          !order.item.toLowerCase().includes(searchTerm) && 
          !order.user.toLowerCase().includes(searchTerm)) {
        return false;
      }

      if (statusFilter && order.status !== statusFilter) {
        return false;
      }

      if (dateFilter) {
        const orderDate = new Date(order.date);
        const today = new Date();
        
        switch (dateFilter) {
          case 'today':
            if (orderDate.toDateString() !== today.toDateString()) return false;
            break;
          case 'week':
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            if (orderDate < weekAgo) return false;
            break;
          case 'month':
            const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
            if (orderDate < monthAgo) return false;
            break;
        }
      }

      return true;
    });

    this.updateDisplay();
  }

  // Update display
  updateDisplay() {
    const emptyState = document.getElementById('empty-state');

    this.orders.forEach(order => {
      order.element.style.display = 'none';
    });

    if (this.filteredOrders.length > 0) {
      this.filteredOrders.forEach(order => {
        order.element.style.display = '';
      });
      emptyState.style.display = 'none';
    } else {
      emptyState.style.display = 'block';
    }
  }

  // Show success message
  showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `${type}-message`;
    messageDiv.innerHTML = `
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path d="M5 12l5 5L20 7" stroke="currentColor" stroke-width="2"/>
      </svg>
      ${message}
    `;

    const main = document.querySelector('.orders-main');
    main.insertBefore(messageDiv, main.firstChild);

    setTimeout(() => {
      messageDiv.remove();
    }, 3000);
  }

  // Update order status in DOM
  updateOrderStatus(orderId, newStatus) {
    const order = this.orders.find(o => o.id === orderId);
    if (order) {
      order.status = newStatus;
      order.element.dataset.status = newStatus;
      
      const statusBadge = order.element.querySelector('.status-badge');
      statusBadge.className = `status-badge ${newStatus}`;
      statusBadge.textContent = newStatus === 'delivered' ? 'Delivered' : 
                               newStatus === 'canceled' ? 'Canceled' : newStatus;

      // Update actions
      const actionsCell = order.element.querySelector('.actions');
      if (newStatus === 'delivered' || newStatus === 'canceled') {
        actionsCell.innerHTML = `
          <button class="action-btn view-btn" onclick="viewOrder('${orderId}')">View</button>
          <button class="action-btn chat-btn" onclick="chatWithCustomer('${order.user}')">Chat</button>
        `;
      }
    }

    this.filterOrders();
  }
}

// Global functions
let ordersManager;

// Manage order function
function manageOrder(orderId, status, paymentMethod) {
  ordersManager.currentOrder = { id: orderId, status, payment: paymentMethod };
  
  const modal = document.getElementById('manage-modal');
  const title = document.getElementById('manage-title');
  const orderDetails = document.getElementById('order-details');
  const paymentSlipSection = document.getElementById('payment-slip-section');
  
  // Find order data
  const order = ordersManager.orders.find(o => o.id === orderId);
  
  title.textContent = `Manage Order ${orderId}`;
  
  // Populate order details
  orderDetails.innerHTML = `
    <div class="detail-row">
      <span class="detail-label">Order ID:</span>
      <span class="detail-value">${orderId}</span>
    </div>
    <div class="detail-row">
      <span class="detail-label">Item:</span>
      <span class="detail-value">${order.item}</span>
    </div>
    <div class="detail-row">
      <span class="detail-label">Customer:</span>
      <span class="detail-value">${order.user}</span>
    </div>
    <div class="detail-row">
      <span class="detail-label">Payment Method:</span>
      <span class="detail-value">${paymentMethod === 'preorder' ? 'Pre-order' : 'Cash on Delivery'}</span>
    </div>
    <div class="detail-row">
      <span class="detail-label">Date Placed:</span>
      <span class="detail-value">${order.date}</span>
    </div>
  `;
  
  // Show payment slip for preorder
  if (paymentMethod === 'preorder') {
    paymentSlipSection.style.display = 'block';
    const paymentSlipImage = document.getElementById('payment-slip-image');
    paymentSlipImage.src = `/images/marketplace/payment-slips/${orderId.replace('#', '')}.jpg`;
  } else {
    paymentSlipSection.style.display = 'none';
  }
  
  modal.style.display = 'flex';
}

// Mark as delivered
function markAsDelivered() {
  if (!ordersManager.currentOrder) return;
  
  const orderId = ordersManager.currentOrder.id;
  
  if (confirm('Are you sure you want to mark this order as delivered?')) {
    showLoading();
    
    // Simulate API call
    setTimeout(() => {
      ordersManager.updateOrderStatus(orderId, 'delivered');
      ordersManager.showMessage('Order marked as delivered successfully!');
      closeManageModal();
      hideLoading();
    }, 1000);
  }
}

// Show cancel form
function showCancelForm() {
  document.querySelector('.manage-actions').style.display = 'none';
  document.getElementById('cancel-form').style.display = 'block';
}

// Hide cancel form
function hideCancelForm() {
  document.querySelector('.manage-actions').style.display = 'flex';
  document.getElementById('cancel-form').style.display = 'none';
  document.getElementById('cancel-reason').value = '';
}

// Confirm cancel order
function confirmCancelOrder() {
  const reason = document.getElementById('cancel-reason').value.trim();
  
  if (!reason) {
    alert('Please provide a reason for cancellation');
    return;
  }
  
  if (!ordersManager.currentOrder) return;
  
  const orderId = ordersManager.currentOrder.id;
  
  showLoading();
  
  // Simulate API call to cancel order and send message
  setTimeout(() => {
    ordersManager.updateOrderStatus(orderId, 'canceled');
    ordersManager.showMessage('Order canceled and notification sent to customer');
    closeManageModal();
    hideLoading();
  }, 1000);
}

// Close manage modal
function closeManageModal() {
  document.getElementById('manage-modal').style.display = 'none';
  hideCancelForm();
  ordersManager.currentOrder = null;
}

// Chat functions
function chatWithCustomer(customerName) {
  const modal = document.getElementById('chat-modal');
  const title = document.getElementById('chat-title');
  
  title.textContent = `Chat with ${customerName}`;
  modal.style.display = 'flex';
}

function closeChatModal() {
  document.getElementById('chat-modal').style.display = 'none';
}

function sendMessage() {
  const input = document.getElementById('message-input');
  const message = input.value.trim();
  
  if (!message) return;
  
  const messagesContainer = document.getElementById('chat-messages');
  const messageElement = document.createElement('div');
  messageElement.className = 'message sent';
  messageElement.innerHTML = `
    <div class="message-content">${message}</div>
    <div class="message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
  `;
  
  messagesContainer.appendChild(messageElement);
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
  
  input.value = '';
}

// View order
function viewOrder(orderId) {
  window.location.href = `/marketplace/seller/orders/${orderId.replace('#', '')}`;
}

// Loading functions
function showLoading() {
  document.getElementById('loading-overlay').style.display = 'flex';
}

function hideLoading() {
  document.getElementById('loading-overlay').style.display = 'none';
}

// Initialize when DOM loads
document.addEventListener('DOMContentLoaded', () => {
  ordersManager = new OrdersManager();
});