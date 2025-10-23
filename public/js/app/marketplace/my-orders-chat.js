/**
 * My Orders Chat UI - Pure Mock implementation
 * No API calls - just UI functionality
 */

class MyOrdersChatUI {
  constructor() {
    this.currentChatOrder = null;
    this.chatMessages = [];
    this.init();
  }

  init() {
    this.setupChatModal();
    this.addChatButtonListeners();
  }

  // Mock chat data for demo
  getMockChatData() {
    return {
      '12345': [
        { id: 1, message: "Hi! I'm working on your custom design. It should be ready by tomorrow.", is_from_seller: true, created_at: '2024-01-25T10:30:00Z' },
        { id: 2, message: "That's great! Can you send me a preview when it's ready?", is_from_seller: false, created_at: '2024-01-25T10:45:00Z' },
        { id: 3, message: "Absolutely! I'll send you photos before printing.", is_from_seller: true, created_at: '2024-01-25T10:46:00Z' }
      ],
      '12346': [
        { id: 4, message: "Your wrist band is ready! When would you like to pick it up?", is_from_seller: true, created_at: '2024-01-25T14:20:00Z' },
        { id: 5, message: "That's awesome! I can come by this afternoon around 3 PM.", is_from_seller: false, created_at: '2024-01-25T14:25:00Z' }
      ],
      '12347': [
        { id: 6, message: "Hope you're enjoying the programming book!", is_from_seller: true, created_at: '2024-01-26T09:15:00Z' },
        { id: 7, message: "Yes, it's exactly what I needed. Thank you so much!", is_from_seller: false, created_at: '2024-01-26T09:20:00Z' }
      ],
      '12348': [
        { id: 8, message: "I understand you want to cancel. No problem at all!", is_from_seller: true, created_at: '2024-01-25T16:30:00Z' },
        { id: 9, message: "Thank you for understanding. I'll order again soon.", is_from_seller: false, created_at: '2024-01-25T16:35:00Z' }
      ],
      // Add fallback for any order ID
      '7': [
        { id: 10, message: "Hello! How can I help you today?", is_from_seller: true, created_at: '2024-01-25T16:30:00Z' },
        { id: 11, message: "Hi! I wanted to check on my order status.", is_from_seller: false, created_at: '2024-01-25T16:35:00Z' }
      ]
    };
  }

  setupChatModal() {
    const modal = document.getElementById('chat-modal');
    const closeButtons = modal?.querySelectorAll('.chat-close, .chat-modal-backdrop');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');

    // Close modal when clicking close button or backdrop
    closeButtons?.forEach(btn => {
      btn.addEventListener('click', () => this.closeChat());
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal?.getAttribute('aria-hidden') === 'false') {
        this.closeChat();
      }
    });

    if (!form || !input) return;

    // Auto-resize textarea
    input.addEventListener('input', () => {
      input.style.height = 'auto';
      input.style.height = Math.min(input.scrollHeight, 120) + 'px';
      
      // Update character counter
      const charCount = document.getElementById('chat-char-count');
      if (charCount) {
        const length = input.value.length;
        charCount.textContent = `${length}/500`;
        
        if (length > 450) {
          charCount.style.color = '#F59E0B';
        } else if (length >= 500) {
          charCount.style.color = '#EF4444';
          input.value = input.value.substring(0, 500);
        } else {
          charCount.style.color = '#9CA3AF';
        }
      }
    });

    // Send on Enter, new line on Shift+Enter
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.sendMessage();
      }
    });

    // Form submit
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.sendMessage();
    });
  }

  addChatButtonListeners() {
    // Add event listeners to existing chat buttons
    document.addEventListener('click', (e) => {
      if (e.target.matches('.btn-chat, .chat-btn') || e.target.closest('.btn-chat, .chat-btn')) {
        e.preventDefault();
        const btn = e.target.matches('.btn-chat, .chat-btn') ? e.target : e.target.closest('.btn-chat, .chat-btn');
        const orderId = btn.dataset.orderId || btn.closest('[data-order-id]')?.dataset.orderId;
        const sellerName = btn.dataset.sellerName || 'Seller';
        
        if (orderId) {
          this.openChat(orderId, sellerName);
        }
      }
    });
  }

  async openChat(orderId, sellerName = 'Seller') {
    console.log('Opening chat for order:', orderId, 'with seller:', sellerName);
    
    // Get order title from DOM if available
    let orderTitle = `Order #${orderId}`;
    const orderRow = document.querySelector(`[data-order-id="${orderId}"]`);
    if (orderRow) {
      const titleElement = orderRow.querySelector('.order-title, .item-name');
      if (titleElement) {
        orderTitle = titleElement.textContent.trim();
      }
    }
    
    this.currentChatOrder = {
      id: orderId,
      seller_name: sellerName,
      title: orderTitle
    };

    const modal = document.getElementById('chat-modal');
    if (!modal) {
      console.error('Chat modal not found');
      return;
    }
    
    // Update modal header
    const titleEl = document.getElementById('chat-modal-title');
    const orderTitleEl = document.getElementById('chat-order-title');
    const orderIdEl = document.getElementById('chat-order-id');
    const avatarEl = document.getElementById('seller-avatar');
    
    if (titleEl) titleEl.textContent = `Chat with ${sellerName}`;
    if (orderTitleEl) orderTitleEl.textContent = orderTitle;
    if (orderIdEl) orderIdEl.textContent = `#${orderId}`;
    if (avatarEl) avatarEl.src = '/images/placeholders/user.png';

    // Show modal
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');

    // Load messages (mock only)
    this.loadChatMessages(orderId);

    // Focus input
    setTimeout(() => {
      const input = document.getElementById('chat-input');
      if (input) input.focus();
    }, 100);
  }

  closeChat() {
    const modal = document.getElementById('chat-modal');
    if (modal) {
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
    }
    
    this.currentChatOrder = null;
    
    // Clear input
    const input = document.getElementById('chat-input');
    if (input) {
      input.value = '';
      input.style.height = 'auto';
    }
    
    const charCount = document.getElementById('chat-char-count');
    if (charCount) {
      charCount.textContent = '0/500';
      charCount.style.color = '#9CA3AF';
    }
  }

  loadChatMessages(orderId) {
    const messagesContainer = document.getElementById('chat-messages');
    if (!messagesContainer) return;

    // Show loading
    messagesContainer.innerHTML = `
      <div class="chat-loading">
        <div class="loading-spinner"></div>
        <span>Loading messages...</span>
      </div>
    `;

    // Simulate loading delay
    setTimeout(() => {
      // Load mock data only
      const mockData = this.getMockChatData();
      this.chatMessages = mockData[orderId] || [];
      
      this.renderMessages();
    }, 800);
  }

  renderMessages() {
    const container = document.getElementById('chat-messages');
    if (!container) return;
    
    if (this.chatMessages.length === 0) {
      container.innerHTML = `
        <div class="chat-empty">
          <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          <p>No messages yet. Start the conversation!</p>
        </div>
      `;
      return;
    }

    container.innerHTML = this.chatMessages.map(msg => `
      <div class="chat-message ${msg.is_from_seller ? 'chat-message--received' : 'chat-message--sent'}">
        <div class="message-content">
          <div class="message-text">${this.escapeHtml(msg.message)}</div>
          <div class="message-time">${this.formatTime(msg.created_at)}</div>
        </div>
      </div>
    `).join('');

    // Scroll to bottom
    setTimeout(() => {
      container.scrollTop = container.scrollHeight;
    }, 50);
  }

  sendMessage() {
    const input = document.getElementById('chat-input');
    if (!input) return;
    
    const message = input.value.trim();
    if (!message || !this.currentChatOrder) return;

    const sendBtn = document.querySelector('.chat-send');
    if (sendBtn) {
      sendBtn.disabled = true;
      sendBtn.innerHTML = `<div class="loading-spinner small"></div>`;
    }

    // Simulate sending delay
    setTimeout(() => {
      // Add message
      const newMessage = {
        id: Date.now(),
        message: message,
        is_from_seller: false,
        created_at: new Date().toISOString()
      };

      this.chatMessages.push(newMessage);

      // Clear input and re-render
      input.value = '';
      input.style.height = 'auto';
      
      const charCount = document.getElementById('chat-char-count');
      if (charCount) {
        charCount.textContent = '0/500';
        charCount.style.color = '#9CA3AF';
      }
      
      this.renderMessages();

      // Reset send button
      if (sendBtn) {
        sendBtn.disabled = false;
        sendBtn.innerHTML = `
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        `;
      }
      
      input.focus();

      // Simulate seller response
      this.simulateSellerResponse();
    }, 500);
  }

  simulateSellerResponse() {
    const responses = [
      "Thank you for reaching out!",
      "I'll get back to you shortly.",
      "Let me check on that for you.",
      "Thanks for your patience!",
      "I appreciate your business!",
      "That's a great question, let me help.",
      "I'll take care of that right away.",
      "Thanks for letting me know!"
    ];

    setTimeout(() => {
      if (this.currentChatOrder && Math.random() > 0.3) {
        const randomResponse = responses[Math.floor(Math.random() * responses.length)];
        const sellerMessage = {
          id: Date.now() + 1,
          message: randomResponse,
          is_from_seller: true,
          created_at: new Date().toISOString()
        };

        this.chatMessages.push(sellerMessage);
        this.renderMessages();
      }
    }, Math.random() * 3000 + 2000);
  }

  formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;
    
    return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
  }

  escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
}

// Global function for inline handlers
function openChat(orderId, sellerName) {
  if (window.myOrdersChatUI) {
    window.myOrdersChatUI.openChat(orderId, sellerName);
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  window.myOrdersChatUI = new MyOrdersChatUI();
});

// Expose globally
window.openChat = openChat;