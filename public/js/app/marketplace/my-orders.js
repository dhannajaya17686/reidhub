class OrdersManager {
  constructor() {
    this.currentTab = 'all';
    this.orders = [];
    this.init();
  }

  init() {
    this.loadOrdersData();
    this.setupEventListeners();
    this.filterOrdersByTab(this.currentTab);
  }

  loadOrdersData() {
    // Sample orders data - in real app, this would come from API
    this.orders = [
      {
        id: '374836393',
        title: 'UCSC Tshirt',
        price: 4000,
        quantity: 2,
        date: 'March 15, 2024',
        status: 'pending',
        statusText: 'Yet to Ship',
        statusMessage: 'Your order is being prepared for shipment',
        image: 'https://via.placeholder.com/120x120/1e3a8a/ffffff?text=UCSC+Tshirt',
        seller: {
          name: 'Students Union of UCSC',
          phone: '+94 11 250 3200',
          email: 'su@ucsc.cmb.ac.lk',
          avatar: 'https://via.placeholder.com/64x64/0466C8/ffffff?text=SU',
          rating: 4.8,
          reviews: 142
        }
      },
      {
        id: '58495749',
        title: 'UCSC Wrist Band',
        price: 600,
        quantity: 1,
        date: 'March 10, 2024',
        status: 'delivered',
        statusText: 'Delivered',
        statusMessage: 'Delivered on March 12, 2024',
        image: 'https://via.placeholder.com/120x120/374151/ffffff?text=Wrist+Band',
        seller: {
          name: 'Students Union of UCSC',
          phone: '+94 11 250 3200',
          email: 'su@ucsc.cmb.ac.lk',
          avatar: 'https://via.placeholder.com/64x64/0466C8/ffffff?text=SU',
          rating: 4.8,
          reviews: 142
        }
      },
      {
        id: '58495750',
        title: 'UOC Cricket Jersey',
        price: 1800,
        quantity: 1,
        date: 'March 8, 2024',
        status: 'shipped',
        statusText: 'Shipped',
        statusMessage: 'Expected delivery: March 20, 2024',
        image: 'https://via.placeholder.com/120x120/16a34a/ffffff?text=Cricket+Jersey',
        seller: {
          name: 'University Sports Club',
          phone: '+94 11 250 3201',
          email: 'sports@ucsc.cmb.ac.lk',
          avatar: 'https://via.placeholder.com/64x64/16a34a/ffffff?text=SC',
          rating: 4.6,
          reviews: 89
        }
      },
      {
        id: '58495751',
        title: 'UCSC Notebook',
        price: 250,
        quantity: 3,
        date: 'March 5, 2024',
        status: 'delivered',
        statusText: 'Delivered',
        statusMessage: 'Delivered on March 7, 2024',
        image: 'https://via.placeholder.com/120x120/dc2626/ffffff?text=Notebook',
        seller: {
          name: 'UCSC Bookstore',
          phone: '+94 11 250 3202',
          email: 'bookstore@ucsc.cmb.ac.lk',
          avatar: 'https://via.placeholder.com/64x64/dc2626/ffffff?text=BS',
          rating: 4.9,
          reviews: 234
        }
      },
      {
        id: '58495752',
        title: 'UCSC Cap',
        price: 800,
        quantity: 1,
        date: 'March 1, 2024',
        status: 'cancelled',
        statusText: 'Cancelled',
        statusMessage: 'Order cancelled by seller - Out of stock',
        image: 'https://via.placeholder.com/120x120/6b7280/ffffff?text=Cancelled',
        seller: {
          name: 'Students Union of UCSC',
          phone: '+94 11 250 3200',
          email: 'su@ucsc.cmb.ac.lk',
          avatar: 'https://via.placeholder.com/64x64/0466C8/ffffff?text=SU',
          rating: 4.8,
          reviews: 142
        }
      }
    ];
  }

  setupEventListeners() {
    // Tab navigation
    document.addEventListener('click', (e) => {
      if (e.target.matches('.tab-button')) {
        this.handleTabChange(e.target);
      }
    });

    // Order actions
    document.addEventListener('click', (e) => {
      const action = e.target.dataset.action;
      const orderId = e.target.dataset.orderId;

      switch (action) {
        case 'contact-seller':
          this.showContactModal(orderId);
          break;
        case 'track-order':
          this.trackOrder(orderId);
          break;
        case 'reorder':
          this.reorderItem(orderId);
          break;
        case 'call':
          this.callSeller();
          break;
        case 'email':
          this.emailSeller();
          break;
      }
    });

    // Modal functionality
    this.setupContactModal();

    // Chat functionality
    this.setupChat();
  }

  handleTabChange(tabButton) {
    const tabId = tabButton.dataset.tab;
    
    // Update active tab
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('tab-button--active');
      btn.setAttribute('aria-selected', 'false');
      btn.setAttribute('tabindex', '-1');
    });
    
    tabButton.classList.add('tab-button--active');
    tabButton.setAttribute('aria-selected', 'true');
    tabButton.setAttribute('tabindex', '0');

    // Update tab content
    this.currentTab = tabId;
    this.filterOrdersByTab(tabId);
  }

  filterOrdersByTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.add('is-hidden');
    });

    // Show target tab content
    const targetTab = document.getElementById(`tab-content-${tabId}`);
    if (targetTab) {
      targetTab.classList.remove('is-hidden');
    }

    // Filter orders based on tab
    let filteredOrders = this.orders;
    
    if (tabId !== 'all') {
      filteredOrders = this.orders.filter(order => {
        switch (tabId) {
          case 'pending':
            return order.status === 'pending';
          case 'shipped':
            return order.status === 'shipped';
          case 'delivered':
            return order.status === 'delivered';
          case 'cancelled':
            return order.status === 'cancelled';
          default:
            return true;
        }
      });
    }

    // Update orders list
    this.renderOrders(filteredOrders, targetTab);
    
    // Update tab counts
    this.updateTabCounts();
  }

  renderOrders(orders, container) {
    if (!container) return;

    let ordersList = container.querySelector('.orders-list');
    if (!ordersList) {
      ordersList = document.createElement('div');
      ordersList.className = 'orders-list';
      container.appendChild(ordersList);
    }

    if (orders.length === 0) {
      ordersList.innerHTML = `
        <div class="empty-state">
          <h3>No orders found</h3>
          <p>You don't have any orders in this category yet.</p>
          <a href="/marketplace" class="btn btn--primary">Start Shopping</a>
        </div>
      `;
      return;
    }

    ordersList.innerHTML = orders.map(order => `
      <article class="order-item" data-order-id="${order.id}">
        <div class="order-image">
          <img src="${order.image}" alt="${order.title}">
        </div>
        
        <div class="order-details">
          <div class="order-header">
            <h3 class="order-title">${order.title}</h3>
            <div class="order-price">Rs. ${order.price.toLocaleString()}</div>
          </div>
          
          <div class="order-meta">
            <div class="order-id">Order ID: #${order.id}</div>
            <div class="order-date">Ordered on: ${order.date}</div>
            <div class="order-quantity">Quantity: ${order.quantity}</div>
          </div>
          
          <div class="order-status">
            <span class="status-badge status-badge--${order.status}">${order.statusText}</span>
            <div class="status-message">${order.statusMessage}</div>
          </div>
        </div>
        
        <div class="order-actions">
          <button class="btn btn--secondary btn--small" data-action="contact-seller" data-order-id="${order.id}">
            Contact Seller
          </button>
          ${this.getOrderActionButton(order)}
        </div>
      </article>
    `).join('');
  }

  getOrderActionButton(order) {
    switch (order.status) {
      case 'pending':
      case 'shipped':
        return `<button class="btn btn--primary btn--small" data-action="track-order" data-order-id="${order.id}">Track Order</button>`;
      case 'delivered':
      case 'cancelled':
        return `<button class="btn btn--outline btn--small" data-action="reorder" data-order-id="${order.id}">Reorder</button>`;
      default:
        return '';
    }
  }

  updateTabCounts() {
    const counts = {
      all: this.orders.length,
      pending: this.orders.filter(o => o.status === 'pending').length,
      shipped: this.orders.filter(o => o.status === 'shipped').length,
      delivered: this.orders.filter(o => o.status === 'delivered').length,
      cancelled: this.orders.filter(o => o.status === 'cancelled').length
    };

    Object.entries(counts).forEach(([tab, count]) => {
      const tabButton = document.querySelector(`[data-tab="${tab}"]`);
      const countElement = tabButton?.querySelector('.tab-count');
      if (countElement) {
        countElement.textContent = count;
      }
    });

    // Update page count
    const ordersCount = document.querySelector('.orders-count');
    if (ordersCount) {
      ordersCount.textContent = `${counts.all} orders found`;
    }
  }

  showContactModal(orderId) {
    const order = this.orders.find(o => o.id === orderId);
    if (!order) return;

    const modal = document.getElementById('contact-modal');
    if (!modal) return;

    // Update seller information
    const sellerName = modal.querySelector('.seller-name');
    const sellerAvatar = modal.querySelector('.seller-avatar img');
    const ratingStars = modal.querySelector('.rating-stars');
    const ratingText = modal.querySelector('.rating-text');
    const phoneValue = modal.querySelector('.contact-option:nth-child(1) .contact-value');
    const emailValue = modal.querySelector('.contact-option:nth-child(2) .contact-value');

    if (sellerName) sellerName.textContent = order.seller.name;
    if (sellerAvatar) sellerAvatar.src = order.seller.avatar;
    if (ratingStars) ratingStars.textContent = '★'.repeat(Math.floor(order.seller.rating));
    if (ratingText) ratingText.textContent = `${order.seller.rating} (${order.seller.reviews} reviews)`;
    if (phoneValue) phoneValue.textContent = order.seller.phone;
    if (emailValue) emailValue.textContent = order.seller.email;

    // Store current order data
    modal.dataset.currentOrder = orderId;

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  setupContactModal() {
    const modal = document.getElementById('contact-modal');
    const closeBtn = document.querySelector('.modal-close');

    if (!modal) return;

    // Close modal function
    const closeModal = () => {
      modal.style.display = 'none';
      document.body.style.overflow = '';
      delete modal.dataset.currentOrder;
    };

    // Close modal events
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    // Close on overlay click
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });

    // Close on escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        closeModal();
      }
    });
  }

  setupChat() {
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.querySelector('.chat-messages');

    if (!chatForm || !chatInput || !chatMessages) return;

    chatForm.addEventListener('submit', (e) => {
      e.preventDefault();
      
      const message = chatInput.value.trim();
      if (!message) return;

      // Add sent message
      this.addChatMessage(message, 'sent');
      
      // Clear input
      chatInput.value = '';
      
      // Simulate response (in real app, this would be real-time)
      setTimeout(() => {
        this.addChatMessage('Thank you for your message! I\'ll get back to you shortly.', 'received');
      }, 1000);
    });
  }

  addChatMessage(text, type) {
    const chatMessages = document.querySelector('.chat-messages');
    if (!chatMessages) return;

    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message chat-message--${type}`;
    
    const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    if (type === 'sent') {
      messageDiv.innerHTML = `
        <div class="message-content">
          <div class="message-text">${text}</div>
          <div class="message-time">${currentTime}</div>
        </div>
      `;
    } else {
      messageDiv.innerHTML = `
        <div class="message-avatar">
          <img src="https://via.placeholder.com/32x32/0466C8/ffffff?text=SU" alt="Seller">
        </div>
        <div class="message-content">
          <div class="message-text">${text}</div>
          <div class="message-time">${currentTime}</div>
        </div>
      `;
    }

    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  trackOrder(orderId) {
    // In real app, this would open tracking details
    console.log('Tracking order:', orderId);
    this.showNotification('Tracking information will be available soon!', 'info');
  }

  reorderItem(orderId) {
    const order = this.orders.find(o => o.id === orderId);
    if (order) {
      console.log('Reordering:', order.title);
      this.showNotification(`${order.title} added to cart!`, 'success');
    }
  }

  callSeller() {
    const modal = document.getElementById('contact-modal');
    const phone = modal.querySelector('.contact-option:nth-child(1) .contact-value').textContent;
    
    // In real app, this would initiate a call
    console.log('Calling:', phone);
    this.showNotification('Opening phone dialer...', 'info');
  }

  emailSeller() {
    const modal = document.getElementById('contact-modal');
    const email = modal.querySelector('.contact-option:nth-child(2) .contact-value').textContent;
    
    // In real app, this would open email client
    window.location.href = `mailto:${email}`;
  }

  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification--${type}`;
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === 'success' ? 'var(--vote-active-bg)' : type === 'error' ? '#FEF2F2' : '#F3F4F6'};
      color: ${type === 'success' ? 'var(--vote-active)' : type === 'error' ? '#DC2626' : '#374151'};
      padding: var(--space-md) var(--space-lg);
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-lg);
      z-index: 1002;
      max-width: 400px;
      font-size: 0.875rem;
      font-weight: 500;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
      if (notification.parentNode) {
        notification.parentNode.removeChild(notification);
      }
    }, 5000);
  }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new OrdersManager();
});