<link rel="stylesheet" href="/css/app/user/marketplace/order-chat.css">

<!-- Main Content Area -->
<main class="chat-main" role="main" aria-label="Order Chat">

  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/dashboard/marketplace/merch-store" class="breadcrumb__link">Marketplace</a>
      </li>
      <li class="breadcrumb__item">
        <a href="/dashboard/marketplace/orders" class="breadcrumb__link">My Orders</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        Order #<?php echo htmlspecialchars($order['id']); ?> Chat
      </li>
    </ol>
  </nav>

  <!-- Chat Container -->
  <div class="chat-container">

    <!-- Left Sidebar: Order Details -->
    <aside class="chat-sidebar">
      <div class="order-details-card">
        <h2 class="sidebar-title">Order Details</h2>

        <!-- Order ID and Status -->
        <div class="detail-section">
          <div class="detail-row">
            <span class="detail-label">Order ID</span>
            <span class="detail-value">#<?php echo htmlspecialchars($order['id']); ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value status status--<?php echo htmlspecialchars($order['status']); ?>">
              <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($order['status']))); ?>
            </span>
          </div>
        </div>

        <!-- Product Information -->
        <div class="detail-section">
          <h3 class="section-subtitle">Product</h3>
          
          <?php if ($order['product_image']): ?>
            <img src="<?php echo htmlspecialchars($order['product_image']); ?>" 
                 alt="<?php echo htmlspecialchars($order['product_title']); ?>" 
                 class="product-thumbnail">
          <?php else: ?>
            <div class="product-thumbnail-placeholder">No Image</div>
          <?php endif; ?>
          
          <div class="product-info">
            <p class="product-title"><?php echo htmlspecialchars($order['product_title']); ?></p>
            <div class="product-meta">
              <span>Qty: <?php echo htmlspecialchars($order['quantity']); ?></span>
              <span>LKR <?php echo number_format($order['unit_price'], 2); ?></span>
            </div>
          </div>
        </div>

        <!-- Participant Information -->
        <div class="detail-section">
          <h3 class="section-subtitle">Participants</h3>
          
          <!-- Buyer Info -->
          <div class="participant">
            <div class="participant-role">Buyer</div>
            <div class="participant-name">
              <?php 
                $buyer_full_name = htmlspecialchars($order['buyer_name'] . ' ' . $order['buyer_last_name']);
                echo $buyer_full_name;
                if ($user_role === 'buyer') {
                  echo ' <span class="you-badge">(You)</span>';
                }
              ?>
            </div>
          </div>

          <!-- Seller Info -->
          <div class="participant">
            <div class="participant-role">Seller</div>
            <div class="participant-name">
              <?php 
                $seller_full_name = htmlspecialchars($order['seller_name'] . ' ' . $order['seller_last_name']);
                echo $seller_full_name;
                if ($user_role === 'seller') {
                  echo ' <span class="you-badge">(You)</span>';
                }
              ?>
            </div>
          </div>
        </div>

        <!-- Order Date -->
        <div class="detail-section">
          <div class="detail-row">
            <span class="detail-label">Ordered On</span>
            <span class="detail-value">
              <?php echo date('M d, Y @ H:i', strtotime($order['created_at'])); ?>
            </span>
          </div>
        </div>

      </div>
    </aside>

    <!-- Right Panel: Messages -->
    <section class="chat-section">
      
      <!-- Messages List -->
      <div class="messages-container" id="messagesContainer">
        <ul class="messages-list" id="messagesList" data-last-message-id="-1">
          <li class="message message--loading">
            <span class="loading-text">Loading messages...</span>
          </li>
        </ul>
      </div>

      <!-- Message Input Form -->
      <form class="chat-input-form" id="chatForm">
        <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
        
        <div class="input-group">
          <textarea 
            id="messageContent"
            name="content" 
            class="message-input" 
            placeholder="Type your message here..." 
            rows="3"
            maxlength="5000"
            required
            aria-label="Message content"></textarea>
          
          <div class="input-footer">
            <span class="char-count">
              <span id="charCount">0</span>/5000
            </span>
            <button type="submit" class="send-button" aria-label="Send message">
              <span>Send</span>
              <svg class="send-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 2L11 13m11-11l-7 20L2 11l17-9z"></path>
              </svg>
            </button>
          </div>
        </div>
      </form>

    </section>

  </div>

</main>

<!-- Message Template -->
<template id="messageTemplate">
  <li class="message">
    <div class="message-header">
      <span class="message-sender"></span>
      <span class="message-time"></span>
    </div>
    <div class="message-content"></div>
  </li>
</template>

<script type="module">
  // Configuration
  const ORDER_ID = <?php echo (int)$order['id']; ?>;
  const POLL_INTERVAL = 2000; // 2 seconds
  const API_BASE = '/dashboard/marketplace/orders';
  
  // DOM Elements
  const messagesList = document.getElementById('messagesList');
  const messagesContainer = document.getElementById('messagesContainer');
  const chatForm = document.getElementById('chatForm');
  const messageInput = document.getElementById('messageContent');
  const charCount = document.getElementById('charCount');
  const messageTemplate = document.getElementById('messageTemplate');
  
  /**
   * Format timestamp to readable format
   */
  function formatTime(timestamp) {
    const date = new Date(timestamp * 1000);
    return new Intl.DateTimeFormat(undefined, { 
      dateStyle: 'medium', 
      timeStyle: 'short' 
    }).format(date);
  }
  
  /**
   * Create message element from data
   */
  function createMessageElement(msg) {
    const element = messageTemplate.content.cloneNode(true);
    
    element.querySelector('.message-sender').textContent = msg.sender_name;
    element.querySelector('.message-time').textContent = formatTime(msg.timestamp);
    element.querySelector('.message-content').textContent = msg.content;
    
    return element;
  }
  
  /**
   * Remove "loading" message if present
   */
  function removeLoadingMessage() {
    const loadingMsg = messagesList.querySelector('.message--loading');
    if (loadingMsg) {
      loadingMsg.remove();
    }
  }
  
  /**
   * Poll for new messages from the server
   */
  async function pollMessages() {
    try {
      const lastMessageId = parseInt(messagesList.dataset.lastMessageId ?? '-1');
      
      const response = await fetch(`${API_BASE}/${ORDER_ID}/chat/get`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          order_id: ORDER_ID,
          last_message_id: lastMessageId
        })
      });
      
      if (!response.ok) return;
      
      const data = await response.json();
      
      if (data.success && data.messages && data.messages.length > 0) {
        removeLoadingMessage();
        
        // Determine scroll position before adding messages
        const shouldScroll = messagesContainer.scrollTop >= 
                            messagesContainer.scrollHeight - messagesContainer.clientHeight - 50;
        
        // Add new messages
        for (const msg of data.messages) {
          const msgElement = createMessageElement(msg);
          messagesList.appendChild(msgElement);
          messagesList.dataset.lastMessageId = msg.id;
        }
        
        // Keep only last 1000 messages
        const items = messagesList.querySelectorAll('.message:not(.message--loading)');
        if (items.length > 1000) {
          for (const item of Array.from(items).slice(0, items.length - 1000)) {
            item.remove();
          }
        }
        
        // Auto-scroll to bottom if user is already at bottom
        if (shouldScroll) {
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
      }
    } catch (error) {
      console.error('Error polling messages:', error);
    }
  }
  
  /**
   * Send message via AJAX
   */
  async function sendMessage(e) {
    e.preventDefault();
    
    const content = messageInput.value.trim();
    
    if (!content) {
      messageInput.focus();
      return;
    }
    
    try {
      // Disable submit button
      const submitBtn = chatForm.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      
      const response = await fetch(`${API_BASE}/${ORDER_ID}/chat/send`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
          order_id: ORDER_ID,
          content: content
        })
      });
      
      const data = await response.json();
      
      if (data.success && data.message) {
        removeLoadingMessage();
        
        // Add message immediately to UI
        const msgElement = createMessageElement(data.message);
        messagesList.appendChild(msgElement);
        messagesList.dataset.lastMessageId = data.message.id;
        
        // Clear input and update char count
        messageInput.value = '';
        charCount.textContent = '0';
        messageInput.focus();
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      } else {
        alert('Error: ' + (data.message || 'Failed to send message'));
      }
      
      submitBtn.disabled = false;
    } catch (error) {
      console.error('Error sending message:', error);
      alert('Error: Failed to send message');
      chatForm.querySelector('button[type="submit"]').disabled = false;
    }
  }
  
  /**
   * Update character count
   */
  messageInput.addEventListener('input', (e) => {
    charCount.textContent = e.target.value.length;
  });
  
  /**
   * Handle form submission
   */
  chatForm.addEventListener('submit', sendMessage);
  
  /**
   * Load initial messages
   */
  async function loadInitialMessages() {
    await pollMessages();
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }
  
  /**
   * Initialize polling
   */
  function initializePolling() {
    loadInitialMessages();
    setInterval(pollMessages, POLL_INTERVAL);
  }
  
  // Start polling when page loads
  document.addEventListener('DOMContentLoaded', initializePolling);
</script>
