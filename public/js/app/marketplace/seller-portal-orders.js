/**
 * Seller Portal Orders: checkout-like popups + status actions + chat functionality
 */

class OrdersManager {
  constructor() {
    this.currentTab = 'all';
    this.orders = [];
    this.filtered = [];
    this.currentOrder = null;
    this.currentChatOrder = null;
    this.chatMessages = [];
    this.chatPollingInterval = null;
    this.isUpdating = false;
    this.useMockChat = true; // Enable mock chat by default
    document.addEventListener('DOMContentLoaded', () => this.init());
  }

  init() {
    this.hookModalBehavior();
    this.hookFilters();
    this.hookModalButtons();
    this.setupChat();
    this.loadOrders();
  }

  // Mock chat data for testing
  getMockChatData() {
    return {
      '12345': [
        { id: 1, message: "Hi! When will my T-shirt be ready?", is_from_seller: false, created_at: '2024-01-25T10:30:00Z' },
        { id: 2, message: "Hello! It will be ready by tomorrow. I'll ship it out first thing in the morning.", is_from_seller: true, created_at: '2024-01-25T10:45:00Z' },
        { id: 3, message: "Perfect! Thank you so much.", is_from_seller: false, created_at: '2024-01-25T10:46:00Z' }
      ],
      '12346': [
        { id: 4, message: "Is the wrist band still available?", is_from_seller: false, created_at: '2024-01-25T14:20:00Z' },
        { id: 5, message: "Yes, it's available! Ready for pickup.", is_from_seller: true, created_at: '2024-01-25T14:25:00Z' }
      ],
      '12347': [
        { id: 6, message: "Thank you for the quick delivery!", is_from_seller: false, created_at: '2024-01-26T09:15:00Z' },
        { id: 7, message: "You're welcome! Hope you enjoy the book.", is_from_seller: true, created_at: '2024-01-26T09:20:00Z' }
      ],
      '12348': [
        { id: 8, message: "Sorry, I need to cancel this order.", is_from_seller: false, created_at: '2024-01-25T16:30:00Z' },
        { id: 9, message: "No problem! I'll process the cancellation.", is_from_seller: true, created_at: '2024-01-25T16:35:00Z' }
      ],
      // Add fallback data for any order ID
      '8': [
        { id: 10, message: "Hello! I have a question about my order.", is_from_seller: false, created_at: '2024-01-25T16:30:00Z' },
        { id: 11, message: "Hi! I'm here to help. What's your question?", is_from_seller: true, created_at: '2024-01-25T16:35:00Z' }
      ]
    };
  }

  async loadOrders() {
    try {
      const res = await fetch('/dashboard/marketplace/seller/orders/get');
      const data = await res.json().catch(() => ({}));
      if (!res.ok || !data?.success) throw new Error(data?.message || 'Failed');

      this.orders = (data.items || []).map(o => ({
        id: String(o.id),
        item: o.title || '',
        user: o.buyer_name || '',
        buyer_id: o.buyer_id || '',
        buyer_avatar: o.buyer_avatar || '/images/placeholders/user.png',
        date: o.created_at || '',
        status: o.status || 'yet-to-ship',
        payment: o.payment || 'cod',
        slip_path: o.slip_path || null,
        unread_messages: o.unread_messages || 0
      }));
      this.filtered = [...this.orders];
      this.renderTable();
      this.updateEmpty();
    } catch (e) {
      console.error(e);
      // Keep existing sample rows; do not overwrite if fetch fails.
      this.setupChatButtonsForSampleData();
    }
  }

  // Setup chat buttons for sample data if server data fails
  setupChatButtonsForSampleData() {
    document.querySelectorAll('.chat-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const orderId = e.currentTarget.dataset.orderId;
        const buyerName = e.currentTarget.dataset.buyerName;
        if (orderId) {
          this.openChat(orderId, buyerName);
        }
      });
    });
  }

  hookModalBehavior() {
    // Overlay click closes (click only if target IS the overlay)
    document.addEventListener('click', (e) => {
      if (!(e.target instanceof HTMLElement)) return;
      if (e.target.id === 'manage-modal') closeManageModal();
      if (e.target.classList.contains('chat-modal-backdrop')) this.closeChat();
    });
    // Esc closes
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeManageModal();
        this.closeChat();
      }
    });
  }

  hookModalButtons() {
    // Close buttons in headers
    document.querySelectorAll('#manage-modal .close-btn').forEach(b => b.addEventListener('click', closeManageModal));
  }

  hookFilters() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.currentTab = btn.dataset.status || 'all';
        this.applyFilters();
      });
    });
    document.getElementById('search-input')?.addEventListener('input', () => this.applyFilters());
    document.getElementById('status-filter')?.addEventListener('change', () => this.applyFilters());
    document.getElementById('date-filter')?.addEventListener('change', () => this.applyFilters());
  }

  applyFilters() {
    const term = (document.getElementById('search-input')?.value || '').toLowerCase();
    const statusSel = document.getElementById('status-filter')?.value || '';
    const dateSel = document.getElementById('date-filter')?.value || '';

    this.filtered = (this.orders.length ? this.orders : this.readExistingRows()).filter(o => {
      if (this.currentTab !== 'all' && o.status !== this.currentTab) return false;
      if (statusSel && o.status !== statusSel) return false;
      if (term) {
        const hay = `${o.id} ${o.item} ${o.user}`.toLowerCase();
        if (!hay.includes(term)) return false;
      }
      if (dateSel) {
        const d = new Date(o.date); const now = new Date();
        if (dateSel === 'today' && d.toDateString() !== now.toDateString()) return false;
        if (dateSel === 'week' && d < new Date(now.getTime() - 7 * 864e5)) return false;
        if (dateSel === 'month' && d < new Date(now.getTime() - 30 * 864e5)) return false;
      }
      return true;
    });

    // If we had server data, re-render; else keep sample rows and just hide unmatched
    if (this.orders.length) {
      this.renderTable();
    } else {
      this.applyFilterToDom();
    }
    this.updateEmpty();
  }

  // Renders from this.filtered (used when server data is present)
  renderTable() {
    const tbody = document.getElementById('orders-tbody');
    if (!tbody) return;
    tbody.innerHTML = this.filtered.map(o => `
      <tr class="order-row" data-status="${o.status}" data-payment="${o.payment}" data-order-id="${o.id}">
        <td class="order-id">#${o.id}</td>
        <td class="item-name">${this.esc(o.item)}</td>
        <td class="user-info"><div class="user-name">${this.esc(o.user)}</div></td>
        <td class="payment-method">
          <span class="payment-badge ${o.payment === 'preorder' ? 'preorder' : 'cod'}">
            ${o.payment === 'preorder' ? 'Pre-order' : 'Cash on Delivery'}
          </span>
        </td>
        <td class="date-placed">${this.esc(o.date)}</td>
        <td class="status">
          <span class="status-badge ${o.status}">
            ${o.status === 'delivered' ? 'Delivered' :
               o.status === 'canceled' ? 'Canceled' :
               o.status === 'returned' ? 'Returned' : 'Yet to ship'}
          </span>
        </td>
        <td class="actions">
          ${o.status === 'delivered' || o.status === 'canceled' ? `
            <button class="action-btn view-btn" onclick="viewOrder('#${o.id}')">View</button>
            <button class="action-btn chat-btn" data-order-id="${o.id}" data-buyer-name="${this.esc(o.user)}">
              Chat
              ${o.unread_messages > 0 ? `<span class="chat-badge">${o.unread_messages}</span>` : ''}
            </button>
          ` : `
            <button class="action-btn manage-btn" onclick="manageOrder('#${o.id}', '${o.status}', '${o.payment}')">Manage</button>
            <button class="action-btn chat-btn" data-order-id="${o.id}" data-buyer-name="${this.esc(o.user)}">
              Chat
              ${o.unread_messages > 0 ? `<span class="chat-badge">${o.unread_messages}</span>` : ''}
            </button>
          `}
        </td>
      </tr>
    `).join('');

    // Add event listeners for chat buttons
    tbody.querySelectorAll('.chat-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const orderId = e.currentTarget.dataset.orderId;
        const buyerName = e.currentTarget.dataset.buyerName;
        this.openChat(orderId, buyerName);
      });
    });
  }

  // Read sample rows present in HTML (so filters still work without server)
  readExistingRows() {
    const rows = Array.from(document.querySelectorAll('#orders-tbody .order-row'));
    return rows.map(r => ({
      id: r.querySelector('.order-id')?.textContent?.replace('#','').trim() || '',
      item: r.querySelector('.item-name')?.textContent?.trim() || '',
      user: r.querySelector('.user-name')?.textContent?.trim() || '',
      buyer_id: r.dataset.buyerId || '',
      buyer_avatar: '/images/placeholders/user.png',
      date: r.querySelector('.date-placed')?.textContent?.trim() || '',
      status: r.getAttribute('data-status') || 'yet-to-ship',
      payment: r.getAttribute('data-payment') || 'cod',
      slip_path: null,
      unread_messages: Math.floor(Math.random() * 3) // Random unread count for demo
    }));
  }

  // Hide/show sample rows according to this.filtered
  applyFilterToDom() {
    const ids = new Set(this.filtered.map(o => String(o.id)));
    document.querySelectorAll('#orders-tbody .order-row').forEach(r => {
      const rid = r.querySelector('.order-id')?.textContent?.replace('#','').trim() || '';
      r.style.display = ids.size ? (ids.has(rid) ? '' : 'none') : '';
    });
  }

  updateStatusLocal(orderIdStr, newStatus) {
    const id = String(orderIdStr).replace('#','');
    const o = this.orders.find(x => x.id === id);
    if (o) o.status = newStatus;
    // Update DOM badge if row exists already
    const row = Array.from(document.querySelectorAll('#orders-tbody .order-row')).find(r => r.querySelector('.order-id')?.textContent?.includes(`#${id}`));
    if (row) {
      row.setAttribute('data-status', newStatus);
      const badge = row.querySelector('.status-badge');
      if (badge) {
        badge.className = `status-badge ${newStatus}`;
        badge.textContent = newStatus === 'delivered' ? 'Delivered' :
                            newStatus === 'canceled' ? 'Canceled' :
                            newStatus === 'returned' ? 'Returned' : 'Yet to ship';
      }
      // Replace Manage with View if finalized
      if (newStatus === 'delivered' || newStatus === 'canceled') {
        const actions = row.querySelector('.actions');
        if (actions) {
          const user = row.querySelector('.user-name')?.textContent || '';
          const unreadBadge = actions.querySelector('.chat-badge');
          const unreadCount = unreadBadge ? unreadBadge.textContent : '';
          actions.innerHTML = `
            <button class="action-btn view-btn" onclick="viewOrder('#${id}')">View</button>
            <button class="action-btn chat-btn" data-order-id="${id}" data-buyer-name="${this.esc(user)}">
              Chat
              ${unreadCount ? `<span class="chat-badge">${unreadCount}</span>` : ''}
            </button>
          `;
          // Re-add event listeners
          const chatBtn = actions.querySelector('.chat-btn');
          if (chatBtn) {
            chatBtn.addEventListener('click', (e) => {
              e.preventDefault();
              const orderId = e.currentTarget.dataset.orderId;
              const buyerName = e.currentTarget.dataset.buyerName;
              this.openChat(orderId, buyerName);
            });
          }
        }
      }
    }
    this.applyFilters();
  }

  updateEmpty() {
    const empty = document.getElementById('empty-state');
    if (!empty) return;
    const anyVisible = Array.from(document.querySelectorAll('#orders-tbody .order-row')).some(r => r.style.display !== 'none');
    empty.style.display = (this.orders.length ? this.filtered.length === 0 : !anyVisible) ? 'block' : 'none';
  }

  // Chat functionality
  setupChat() {
    const modal = document.getElementById('chat-modal');
    const closeBtn = modal?.querySelector('.chat-close');
    const backdrop = modal?.querySelector('.chat-modal-backdrop');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');

    // Close chat handlers
    [closeBtn, backdrop].forEach(el => {
      if (el) {
        el.addEventListener('click', () => this.closeChat());
      }
    });

    if (!form || !input) return;

    // Auto-resize textarea
    input.addEventListener('input', () => {
      input.style.height = 'auto';
      input.style.height = Math.min(input.scrollHeight, 120) + 'px';
      
      // Update character counter if it exists
      const charCount = document.getElementById('chat-char-count');
      if (charCount) {
        charCount.textContent = input.value.length;
      }
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

  async openChat(orderId, buyerName) {
    console.log('Opening chat for order:', orderId, 'with buyer:', buyerName);
    
    const order = this.orders.find(o => o.id === orderId) || this.readExistingRows().find(o => o.id === orderId);
    if (!order) {
      console.error('Order not found:', orderId);
      return;
    }

    // Create order object with buyer name if not from server data
    const chatOrder = {
      id: orderId,
      item: order.item,
      user: buyerName || order.user,
      buyer_avatar: order.buyer_avatar || '/images/placeholders/user.png'
    };

    this.currentChatOrder = chatOrder;
    const modal = document.getElementById('chat-modal');
    
    if (!modal) {
      console.error('Chat modal not found in DOM');
      return;
    }
    
    // Update modal header
    const titleEl = document.getElementById('chat-modal-title');
    const orderTitleEl = document.getElementById('chat-order-title');
    const orderIdEl = document.getElementById('chat-order-id');
    const avatarEl = document.getElementById('buyer-avatar');
    
    if (titleEl) titleEl.textContent = `Chat with ${chatOrder.user}`;
    if (orderTitleEl) orderTitleEl.textContent = chatOrder.item;
    if (orderIdEl) orderIdEl.textContent = `#${chatOrder.id}`;
    if (avatarEl) avatarEl.src = chatOrder.buyer_avatar;

    // Show modal using simple class toggle
    modal.classList.add('show');
    modal.style.display = 'flex';
    document.body.classList.add('modal-open');

    // Load messages after showing modal
    await this.loadChatMessages(orderId);

    // Focus input
    setTimeout(() => {
      const input = document.getElementById('chat-input');
      if (input) input.focus();
    }, 100);
  }

  closeChat() {
    const modal = document.getElementById('chat-modal');
    if (modal) {
      modal.classList.remove('show');
      modal.style.display = 'none';
      document.body.classList.remove('modal-open');
    }
    
    this.currentChatOrder = null;
    this.stopChatPolling();
    
    // Clear input
    const input = document.getElementById('chat-input');
    if (input) {
      input.value = '';
      input.style.height = 'auto';
    }
    const charCount = document.getElementById('chat-char-count');
    if (charCount) {
      charCount.textContent = '0';
    }
  }

  async loadChatMessages(orderId) {
    const messagesContainer = document.getElementById('chat-messages');
    if (!messagesContainer) return;

    // Show loading state
    messagesContainer.innerHTML = `
      <div class="chat-loading">
        <div class="loading-spinner"></div>
        <span>Loading messages...</span>
      </div>
    `;

    if (this.useMockChat) {
      // Use mock data - simulate loading delay
      await new Promise(resolve => setTimeout(resolve, 800));
      
      const mockData = this.getMockChatData();
      this.chatMessages = mockData[orderId] || [];
      this.renderChatMessages();
      
      // Mark as read (mock)
      await this.markMessagesAsRead(orderId);
      return;
    }

    // Try real server endpoint
    try {
      const res = await fetch(`/dashboard/marketplace/seller/orders/${orderId}/messages`);
      
      if (!res.ok) {
        if (res.status === 404) {
          throw new Error('Chat endpoint not found. Using mock data instead.');
        }
        throw new Error(`Server error: ${res.status}`);
      }

      let data;
      const contentType = res.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        data = await res.json();
      } else {
        const text = await res.text();
        console.error('Non-JSON response received:', text.substring(0, 200));
        throw new Error('Invalid response format from server');
      }
      
      if (!data?.success) {
        throw new Error(data?.message || 'Failed to load messages');
      }

      this.chatMessages = data.messages || [];
      this.renderChatMessages();
      await this.markMessagesAsRead(orderId);
      
    } catch (error) {
      console.warn('Server chat unavailable, using mock data:', error.message);
      
      // Fall back to mock data
      const mockData = this.getMockChatData();
      this.chatMessages = mockData[orderId] || [];
      this.renderChatMessages();
      
      // Show a subtle notice that it's demo mode
      if (this.chatMessages.length === 0) {
        messagesContainer.innerHTML = `
          <div class="chat-empty">
            <p><em>Demo Mode: Chat backend not available</em></p>
            <p>Start the conversation to see how it works!</p>
          </div>
        `;
      }
    }
  }

  renderChatMessages() {
    const container = document.getElementById('chat-messages');
    if (!container) return;
    
    if (!this.chatMessages.length) {
      container.innerHTML = `
        <div class="chat-empty">
          <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          <p>No messages yet. Start the conversation!</p>
        </div>
      `;
      return;
    }

    container.innerHTML = this.chatMessages.map(msg => `
      <div class="chat-message ${msg.is_from_seller ? 'chat-message--sent' : 'chat-message--received'}" data-message-id="${msg.id}">
        <div class="message-content">
          <div class="message-text">${this.esc(msg.message)}</div>
          <div class="message-time">${this.formatTime(msg.created_at)}</div>
        </div>
      </div>
    `).join('');

    // Scroll to bottom
    setTimeout(() => {
      container.scrollTop = container.scrollHeight;
    }, 50);
  }

  async sendMessage() {
    const input = document.getElementById('chat-input');
    if (!input) return;
    
    const message = input.value.trim();
    if (!message || !this.currentChatOrder) return;

    const sendBtn = document.querySelector('.chat-send');
    if (sendBtn) {
      sendBtn.disabled = true;
      sendBtn.textContent = 'Sending...';
    }

    if (this.useMockChat) {
      // Mock send - simulate delay
      await new Promise(resolve => setTimeout(resolve, 500));
      
      // Add message to mock data
      const newMessage = {
        id: Date.now(),
        message: message,
        is_from_seller: true,
        created_at: new Date().toISOString()
      };

      this.chatMessages.push(newMessage);

      // Clear input and re-render
      input.value = '';
      input.style.height = 'auto';
      const charCount = document.getElementById('chat-char-count');
      if (charCount) charCount.textContent = '0';
      
      this.renderChatMessages();

      // Reset button
      if (sendBtn) {
        sendBtn.disabled = false;
        sendBtn.textContent = 'Send';
      }
      input.focus();

      // Simulate buyer response after 2-4 seconds
      this.simulateBuyerResponse();
      return;
    }

    // Try real server endpoint
    try {
      const res = await fetch(`/dashboard/marketplace/seller/orders/${this.currentChatOrder.id}/messages`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ message })
      });

      if (!res.ok) {
        if (res.status === 404) {
          throw new Error('Chat endpoint not found. Please contact support.');
        }
        throw new Error(`Server error: ${res.status}`);
      }

      let data;
      const contentType = res.headers.get('content-type');
      if (contentType && contentType.includes('application/json')) {
        data = await res.json();
      } else {
        throw new Error('Invalid response format from server');
      }
      
      if (!data?.success) {
        throw new Error(data?.message || 'Failed to send message');
      }

      // Add message to local array
      this.chatMessages.push({
        id: data.message.id || Date.now(),
        message: message,
        is_from_seller: true,
        created_at: new Date().toISOString()
      });

      // Clear input and re-render
      input.value = '';
      input.style.height = 'auto';
      const charCount = document.getElementById('chat-char-count');
      if (charCount) charCount.textContent = '0';
      
      this.renderChatMessages();

    } catch (error) {
      console.error('Error sending message:', error);
      alert(`Failed to send message: ${error.message}`);
    } finally {
      if (sendBtn) {
        sendBtn.disabled = false;
        sendBtn.textContent = 'Send';
      }
      input.focus();
    }
  }

  // Simulate buyer response for demo
  simulateBuyerResponse() {
    if (!this.useMockChat || !this.currentChatOrder) return;
    
    const responses = [
      "Thank you for the update!",
      "That sounds good.",
      "Perfect, looking forward to it.",
      "Great, thanks!",
      "Understood, no problem.",
      "Awesome, thank you so much!",
      "Got it, thanks for letting me know.",
      "Excellent service!"
    ];

    setTimeout(() => {
      if (this.currentChatOrder && Math.random() > 0.3) { // 70% chance of response
        const randomResponse = responses[Math.floor(Math.random() * responses.length)];
        const buyerMessage = {
          id: Date.now() + 1,
          message: randomResponse,
          is_from_seller: false,
          created_at: new Date().toISOString()
        };

        this.chatMessages.push(buyerMessage);
        this.renderChatMessages();
        console.log('Buyer responded:', randomResponse);
      }
    }, Math.random() * 3000 + 2000); // 2-5 seconds delay
  }

  startChatPolling() {
    // Disable polling when using mock data
    if (this.useMockChat) return;
    
    if (this.chatPollingInterval) return;
    
    this.chatPollingInterval = setInterval(async () => {
      if (!this.currentChatOrder) return;
      
      try {
        const res = await fetch(`/dashboard/marketplace/seller/orders/${this.currentChatOrder.id}/messages?since=${this.getLastMessageTime()}`);
        
        if (!res.ok) {
          if (res.status !== 404) {
            console.warn(`Polling failed: ${res.status}`);
          }
          return;
        }

        const contentType = res.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          console.warn('Non-JSON response during polling');
          return;
        }

        const data = await res.json();
        
        if (data?.success && data.messages?.length) {
          this.chatMessages.push(...data.messages);
          this.renderChatMessages();
        }
      } catch (error) {
        if (!error.message.includes('404')) {
          console.error('Error polling messages:', error);
        }
      }
    }, 5000);
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
    if (this.useMockChat) {
      // Mock mark as read
      const order = this.orders.find(o => o.id === orderId);
      if (order) {
        order.unread_messages = 0;
      }
      
      // Remove chat badges from UI
      const chatBadges = document.querySelectorAll(`[data-order-id="${orderId}"] .chat-badge`);
      chatBadges.forEach(badge => {
        badge.style.display = 'none';
      });
      return;
    }

    // Try real endpoint
    try {
      const res = await fetch(`/dashboard/marketplace/seller/orders/${orderId}/messages/read`, {
        method: 'POST'
      });
      
      if (!res.ok || !res.headers.get('content-type')?.includes('application/json')) {
        return;
      }

      const data = await res.json();
      if (data?.success) {
        const order = this.orders.find(o => o.id === orderId);
        if (order) {
          order.unread_messages = 0;
          this.applyFilters();
        }
      }
    } catch (error) {
      console.debug('Mark as read not available:', error.message);
    }
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

/* ---------- Modal controls (match your markup) ---------- */

function manageOrder(orderId, status, paymentMethod) {
  // Use .manage-modal (your panel class)
  const overlay = document.getElementById('manage-modal');
  const panel = overlay?.querySelector('.manage-modal');
  const title = document.getElementById('manage-title');
  const orderDetails = document.getElementById('order-details');
  const slipSection = document.getElementById('payment-slip-section');

  window._ordersManager = window._ordersManager || new OrdersManager(); // ensure instance
  const mgr = window._ordersManager;

  mgr.currentOrder = { id: orderId, status, payment: paymentMethod };

  // Find order from state or from DOM sample rows
  const id = String(orderId).replace('#','');
  const fromState = (mgr.orders.find(x => x.id === id) || mgr.readExistingRows().find(x => x.id === id));
  if (!overlay || !panel || !title || !orderDetails || !fromState) return;

  title.textContent = `Manage Order ${orderId}`;
  orderDetails.innerHTML = `
    <div class="detail-row"><span class="detail-label">Order ID:</span><span class="detail-value">${orderId}</span></div>
    <div class="detail-row"><span class="detail-label">Item:</span><span class="detail-value">${mgr.esc(fromState.item)}</span></div>
    <div class="detail-row"><span class="detail-label">Customer:</span><span class="detail-value">${mgr.esc(fromState.user)}</span></div>
    <div class="detail-row"><span class="detail-label">Payment Method:</span><span class="detail-value">${paymentMethod === 'preorder' ? 'Pre-order' : 'Cash on Delivery'}</span></div>
    <div class="detail-row"><span class="detail-label">Date Placed:</span><span class="detail-value">${mgr.esc(fromState.date)}</span></div>
  `;

  if (paymentMethod === 'preorder' && fromState.slip_path && slipSection) {
    slipSection.style.display = 'block';
    const img = document.getElementById('payment-slip-image');
    if (img) img.src = fromState.slip_path;
  } else if (slipSection) {
    slipSection.style.display = 'none';
  }

  overlay.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

async function markAsDelivered() {
  const mgr = window._ordersManager;
  if (!mgr?.currentOrder || mgr.isUpdating) return;
  const id = mgr.currentOrder.id.replace('#','');
  if (!confirm('Mark this order as delivered?')) return;

  mgr.isUpdating = true;
  try {
    const fd = new FormData(); fd.append('order_id', id);
    const res = await fetch('/dashboard/marketplace/seller/orders/mark-delivered', { method: 'POST', body: fd });
    const json = await res.json().catch(() => ({}));
    if (!res.ok || !json?.success) throw new Error(json?.message || 'Failed');
    mgr.updateStatusLocal(id, 'delivered');
    closeManageModal();
  } catch (e) {
    alert('Failed to update order.');
  } finally {
    mgr.isUpdating = false;
  }
}

async function confirmCancelOrder() {
  const mgr = window._ordersManager;
  if (!mgr?.currentOrder || mgr.isUpdating) return;
  const id = mgr.currentOrder.id.replace('#','');
  const reason = (document.getElementById('cancel-reason')?.value || '').trim();
  if (!reason) { alert('Please provide a reason for cancellation'); return; }

  mgr.isUpdating = true;
  try {
    const fd = new FormData(); fd.append('order_id', id); fd.append('reason', reason);
    const res = await fetch('/dashboard/marketplace/seller/orders/cancel', { method: 'POST', body: fd });
    const json = await res.json().catch(() => ({}));
    if (!res.ok || !json?.success) throw new Error(json?.message || 'Failed');
    mgr.updateStatusLocal(id, 'canceled');
    closeManageModal();
  } catch (e) {
    alert('Failed to cancel order.');
  } finally {
    mgr.isUpdating = false;
  }
}

function showCancelForm() {
  document.querySelector('.manage-actions')?.style && (document.querySelector('.manage-actions').style.display = 'none');
  document.getElementById('cancel-form')?.style && (document.getElementById('cancel-form').style.display = 'block');
}
function hideCancelForm() {
  document.querySelector('.manage-actions')?.style && (document.querySelector('.manage-actions').style.display = 'flex');
  const cf = document.getElementById('cancel-form'); if (cf?.style) cf.style.display = 'none';
  const input = document.getElementById('cancel-reason'); if (input) input.value = '';
}
function closeManageModal() {
  const overlay = document.getElementById('manage-modal');
  if (overlay) overlay.style.display = 'none';
  document.body.style.overflow = '';
  hideCancelForm();
  const mgr = window._ordersManager; if (mgr) mgr.currentOrder = null;
}

/* View order navigation */
function viewOrder(orderIdStr) {
  const id = String(orderIdStr).replace('#','');
  window.location.href = `/marketplace/seller/orders/${id}`;
}

/* Expose for inline handlers */
window.manageOrder = manageOrder;
window.markAsDelivered = markAsDelivered;
window.confirmCancelOrder = confirmCancelOrder;
window.showCancelForm = showCancelForm;
window.hideCancelForm = hideCancelForm;
window.closeManageModal = closeManageModal;
window.viewOrder = viewOrder;

// Boot
window._ordersManager = new OrdersManager();