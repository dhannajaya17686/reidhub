class OrdersManager {
  constructor() {
    this.currentTab = 'all';
    this.orders = [];
    this.currentChatOrder = null;
    this.chatMessages = [];
    this.chatPollingInterval = null;
    document.addEventListener('DOMContentLoaded', () => this.init());
  }

  init() {
    this.loadOrdersData();
    this.setupTabs();
    this.setupChat();
  }

  async loadOrdersData() {
    try {
      const res = await fetch('/dashboard/marketplace/orders/get');
      const data = await res.json();
      if (!res.ok || !data?.success) throw new Error(data?.message || 'Failed to load orders');

      this.orders = (data.items || []).map(o => ({
        id: String(o.id),
        title: o.title,
        price: Number(o.price),
        quantity: Number(o.quantity),
        ordered_at: o.ordered_at,
        status: o.status,           // pending | shipped | delivered | cancelled
        statusText: o.statusText,
        statusMessage: o.statusMessage || '',
        image: o.image || '/images/placeholders/product.png',
        seller_id: o.seller_id,
        seller_name: o.seller_name || 'Seller',
        seller_avatar: o.seller_avatar || '/images/placeholders/user.png',
        unread_messages: o.unread_messages || 0
      }));

      this.updateTabCounts();
      this.filterOrdersByTab(this.currentTab);
    } catch (e) {
      console.error(e);
      this.renderEmpty(document.getElementById('tab-content-all')?.querySelector('.orders-list'));
      this.updateTabCounts();
    }
  }

  setupTabs() {
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.tab-button');
      if (!btn) return;
      this.switchTab(btn);
    });
  }

  switchTab(tabButton) {
    const tabId = tabButton.dataset.tab;
    document.querySelectorAll('.tab-button').forEach(btn => {
      btn.classList.remove('tab-button--active');
      btn.setAttribute('aria-selected', 'false');
      btn.setAttribute('tabindex', '-1');
    });
    tabButton.classList.add('tab-button--active');
    tabButton.setAttribute('aria-selected', 'true');
    tabButton.setAttribute('tabindex', '0');

    this.currentTab = tabId;
    this.filterOrdersByTab(tabId);
  }

  filterOrdersByTab(tabId) {
    // Hide/show panes
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('is-hidden'));
    const target = document.getElementById(`tab-content-${tabId}`);
    if (target) target.classList.remove('is-hidden');

    // Filter
    const filtered = tabId === 'all' ? this.orders : this.orders.filter(o => o.status === tabId);
    this.renderOrders(filtered, target?.querySelector('.orders-list'));
  }

  renderOrders(orders, container) {
    if (!container) return;
    if (!orders.length) {
      this.renderEmpty(container);
      return;
    }

    container.innerHTML = orders.map(order => `
      <article class="order-item" data-order-id="${order.id}">
        <div class="order-image">
          <img src="${order.image}" alt="${this.esc(order.title)}" onerror="this.src='/images/placeholders/product.png'">
        </div>
        <div class="order-details">
          <div class="order-header">
            <h3 class="order-title">${this.esc(order.title)}</h3>
            <div class="order-price">Rs. ${order.price.toLocaleString()}</div>
          </div>
          <div class="order-meta">
            <div class="order-id">Order ID: #${order.id}</div>
            <div class="order-date">Ordered on: ${this.formatDate(order.ordered_at)}</div>
            <div class="order-quantity">Quantity: ${order.quantity}</div>
          </div>
          <div class="order-status">
            <span class="status-badge status-badge--${order.status}">${order.statusText}</span>
            <div class="status-message">${this.esc(order.statusMessage)}</div>
          </div>
        </div>
        <div class="order-actions">
          <button class="btn btn-chat" data-order-id="${order.id}" title="Chat with seller">
            <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
            </svg>
            Chat
            ${order.unread_messages > 0 ? `<span class="chat-badge">${order.unread_messages}</span>` : ''}
          </button>
        </div>
      </article>
    `).join('');

    // Add event listeners for chat buttons
    container.querySelectorAll('.btn-chat').forEach(btn => {
      btn.addEventListener('click', (e) => {
        const orderId = e.currentTarget.dataset.orderId;
        this.openChat(orderId);
      });
    });
  }

  renderEmpty(container) {
    if (!container) return;
    container.innerHTML = `
      <div class="empty-state">
        <h3>No orders found</h3>
        <p>You don't have any orders in this category yet.</p>
        <a href="/dashboard/marketplace/merch-store" class="btn btn--primary">Start Shopping</a>
      </div>
    `;
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
      const btn = document.querySelector(`[data-tab="${tab}"]`);
      const span = btn?.querySelector('.tab-count');
      if (span) span.textContent = count;
    });

    const headerCount = document.querySelector('.orders-count');
    if (headerCount) headerCount.textContent = `${counts.all} orders found`;
  }

  // Chat functionality
  setupChat() {
    const modal = document.getElementById('chat-modal');
    const closeBtn = modal.querySelector('.chat-close');
    const backdrop = modal.querySelector('.chat-modal-backdrop');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const charCount = document.getElementById('chat-char-count');

    // Close chat handlers
    [closeBtn, backdrop].forEach(el => {
      el.addEventListener('click', () => this.closeChat());
    });

    // ESC key to close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !modal.getAttribute('aria-hidden')) {
        this.closeChat();
      }
    });

    // Auto-resize textarea
    input.addEventListener('input', () => {
      input.style.height = 'auto';
      input.style.height = Math.min(input.scrollHeight, 120) + 'px';
      charCount.textContent = input.value.length;
    });

    // Send message on Enter (but allow Shift+Enter for new line)
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        form.dispatchEvent(new Event('submit'));
      }
    });

    // Form submit
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.sendMessage();
    });
  }

  async openChat(orderId) {
    const order = this.orders.find(o => o.id === orderId);
    if (!order) return;

    this.currentChatOrder = order;
    const modal = document.getElementById('chat-modal');
    
    // Update modal header
    document.getElementById('chat-modal-title').textContent = `Chat with ${order.seller_name}`;
    document.getElementById('chat-order-title').textContent = order.title;
    document.getElementById('chat-order-id').textContent = `#${order.id}`;
    document.getElementById('seller-avatar').src = order.seller_avatar;

    // Show modal
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');

    // Load chat messages
    await this.loadChatMessages(orderId);

    // Start polling for new messages
    this.startChatPolling();

    // Focus input
    setTimeout(() => {
      document.getElementById('chat-input').focus();
    }, 100);
  }

  closeChat() {
    const modal = document.getElementById('chat-modal');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    
    this.currentChatOrder = null;
    this.stopChatPolling();
    
    // Clear input
    const input = document.getElementById('chat-input');
    input.value = '';
    input.style.height = 'auto';
    document.getElementById('chat-char-count').textContent = '0';
  }

  async loadChatMessages(orderId) {
    const messagesContainer = document.getElementById('chat-messages');
    messagesContainer.innerHTML = `
      <div class="chat-loading">
        <div class="loading-spinner"></div>
        <span>Loading messages...</span>
      </div>
    `;

    try {
      const res = await fetch(`/dashboard/marketplace/orders/${orderId}/messages`);
      const data = await res.json();
      
      if (!res.ok || !data?.success) {
        throw new Error(data?.message || 'Failed to load messages');
      }

      this.chatMessages = data.messages || [];
      this.renderChatMessages();

      // Mark messages as read
      await this.markMessagesAsRead(orderId);
      
    } catch (error) {
      console.error('Error loading messages:', error);
      messagesContainer.innerHTML = `
        <div class="chat-error">
          <p>Failed to load messages. Please try again.</p>
          <button class="btn btn-sm" onclick="window.ordersManager.loadChatMessages('${orderId}')">Retry</button>
        </div>
      `;
    }
  }

  renderChatMessages() {
    const container = document.getElementById('chat-messages');
    
    if (!this.chatMessages.length) {
      container.innerHTML = `
        <div class="chat-empty">
          <p>No messages yet. Start the conversation!</p>
        </div>
      `;
      return;
    }

    container.innerHTML = this.chatMessages.map(msg => `
      <div class="chat-message ${msg.is_from_buyer ? 'chat-message--sent' : 'chat-message--received'}" data-message-id="${msg.id}">
        <div class="message-content">
          <div class="message-text">${this.esc(msg.message)}</div>
          <div class="message-time">${this.formatTime(msg.created_at)}</div>
        </div>
      </div>
    `).join('');

    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
  }

  async sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message || !this.currentChatOrder) return;

    const sendBtn = document.querySelector('.chat-send');
    sendBtn.disabled = true;

    try {
      const res = await fetch(`/dashboard/marketplace/orders/${this.currentChatOrder.id}/messages`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message })
      });

      const data = await res.json();
      
      if (!res.ok || !data?.success) {
        throw new Error(data?.message || 'Failed to send message');
      }

      // Add message to local array
      this.chatMessages.push({
        id: data.message.id,
        message: message,
        is_from_buyer: true,
        created_at: new Date().toISOString()
      });

      // Clear input and re-render
      input.value = '';
      input.style.height = 'auto';
      document.getElementById('chat-char-count').textContent = '0';
      this.renderChatMessages();

    } catch (error) {
      console.error('Error sending message:', error);
      alert('Failed to send message. Please try again.');
    } finally {
      sendBtn.disabled = false;
      input.focus();
    }
  }

  startChatPolling() {
    if (this.chatPollingInterval) return;
    
    this.chatPollingInterval = setInterval(async () => {
      if (!this.currentChatOrder) return;
      
      try {
        const res = await fetch(`/dashboard/marketplace/orders/${this.currentChatOrder.id}/messages?since=${this.getLastMessageTime()}`);
        const data = await res.json();
        
        if (res.ok && data?.success && data.messages?.length) {
          this.chatMessages.push(...data.messages);
          this.renderChatMessages();
        }
      } catch (error) {
        console.error('Error polling messages:', error);
      }
    }, 3000); // Poll every 3 seconds
  }

  stopChatPolling() {
    if (this.chatPollingInterval) {
      clearInterval(this.chatPollingInterval);
      this.chatPollingInterval = null;
    }
  }

  getLastMessageTime() {
    if (!this.chatMessages.length) return '';
    return this.chatMessages[this.chatMessages.length - 1].created_at;
  }

  async markMessagesAsRead(orderId) {
    try {
      await fetch(`/dashboard/marketplace/orders/${orderId}/messages/read`, {
        method: 'POST'
      });
      
      // Update unread count in UI
      const order = this.orders.find(o => o.id === orderId);
      if (order) {
        order.unread_messages = 0;
        // Re-render current tab to update badge
        this.filterOrdersByTab(this.currentTab);
      }
    } catch (error) {
      console.error('Error marking messages as read:', error);
    }
  }

  formatDate(s) {
    const d = new Date(s);
    if (Number.isNaN(d.getTime())) return this.esc(String(s || ''));
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
  }

  formatTime(s) {
    const d = new Date(s);
    if (Number.isNaN(d.getTime())) return '';
    
    const now = new Date();
    const diffMs = now - d;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;
    
    return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
  }

  esc(s) {
    return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }
}

// Init and expose globally for retry functionality
window.ordersManager = new OrdersManager();