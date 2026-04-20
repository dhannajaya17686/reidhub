<link rel="stylesheet" href="/css/app/user/marketplace/order-chat.css">

<main class="chat-main" role="main" aria-label="Marketplace Report Chat">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/dashboard/marketplace/admin/analytics" class="breadcrumb__link">Marketplace Admin</a>
      </li>
      <li class="breadcrumb__item">
        <a href="/dashboard/marketplace/admin/sellers" class="breadcrumb__link">Seller Moderation</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        Report #<?php echo (int)$report['id']; ?> Chat
      </li>
    </ol>
  </nav>

  <div class="chat-container">
    <aside class="chat-sidebar">
      <div class="order-details-card">
        <h2 class="sidebar-title">Report Details</h2>

        <div class="detail-section">
          <div class="detail-row">
            <span class="detail-label">Report ID</span>
            <span class="detail-value">#<?php echo (int)$report['id']; ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Order ID</span>
            <span class="detail-value"><?php echo isset($report['order_id']) && $report['order_id'] !== null ? ('#' . (int)$report['order_id']) : 'N/A'; ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value status status--<?php echo htmlspecialchars((string)$report['status']); ?>">
              <?php echo ucfirst(str_replace('-', ' ', htmlspecialchars((string)$report['status']))); ?>
            </span>
          </div>
        </div>

        <div class="detail-section">
          <h3 class="section-subtitle">Item</h3>
          <?php
            $images = json_decode((string)($report['product_images'] ?? '[]'), true);
            $firstImage = (is_array($images) && !empty($images)) ? $images[0] : null;
          ?>
          <?php if ($firstImage): ?>
            <img src="<?php echo htmlspecialchars($firstImage); ?>" alt="<?php echo htmlspecialchars((string)$report['product_title']); ?>" class="product-thumbnail">
          <?php else: ?>
            <div class="product-thumbnail-placeholder">No Image</div>
          <?php endif; ?>
          <div class="product-info">
            <p class="product-title"><?php echo htmlspecialchars((string)$report['product_title']); ?></p>
            <div class="product-meta">
              <span>Category: <?php echo htmlspecialchars((string)$report['category']); ?></span>
            </div>
          </div>
        </div>

        <div class="detail-section">
          <h3 class="section-subtitle">Reason</h3>
          <p class="product-title"><?php echo nl2br(htmlspecialchars((string)$report['reason'])); ?></p>
        </div>

        <div class="detail-section">
          <h3 class="section-subtitle">Participants</h3>
          <div class="participant">
            <div class="participant-role">Admin</div>
            <div class="participant-name">Moderation Team <span class="you-badge">(You)</span></div>
          </div>
          <div class="participant">
            <div class="participant-role">Seller</div>
            <div class="participant-name"><?php echo htmlspecialchars(trim((string)$report['seller_first_name'] . ' ' . (string)$report['seller_last_name'])); ?></div>
          </div>
        </div>
      </div>
    </aside>

    <section class="chat-section">
      <div class="messages-container" id="messagesContainer">
        <ul class="messages-list" id="messagesList" data-last-message-id="-1">
          <li class="message message--loading">
            <span class="loading-text">Loading messages...</span>
          </li>
        </ul>
      </div>

      <form class="chat-input-form" id="chatForm">
        <input type="hidden" name="report_id" value="<?php echo (int)$report['id']; ?>">
        <div class="input-group">
          <textarea id="messageContent" name="content" class="message-input" placeholder="Type your message for seller..." rows="3" maxlength="5000" required></textarea>
          <div class="input-footer">
            <span class="char-count"><span id="charCount">0</span>/5000</span>
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
  const REPORT_ID = <?php echo (int)$report['id']; ?>;
  const API_BASE = '/dashboard/marketplace/admin/reported';
  const POLL_INTERVAL = 2000;

  const messagesList = document.getElementById('messagesList');
  const messagesContainer = document.getElementById('messagesContainer');
  const chatForm = document.getElementById('chatForm');
  const messageInput = document.getElementById('messageContent');
  const charCount = document.getElementById('charCount');
  const messageTemplate = document.getElementById('messageTemplate');

  function formatTime(timestamp) {
    const date = new Date(Number(timestamp) * 1000);
    return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(date);
  }

  function createMessageElement(msg) {
    const element = messageTemplate.content.cloneNode(true);
    element.querySelector('.message-sender').textContent = msg.sender_name || 'Unknown';
    element.querySelector('.message-time').textContent = formatTime(msg.timestamp || 0);
    element.querySelector('.message-content').textContent = msg.content || '';
    return element;
  }

  function removeLoadingMessage() {
    const loadingMsg = messagesList.querySelector('.message--loading');
    if (loadingMsg) loadingMsg.remove();
  }

  async function pollMessages() {
    try {
      const lastMessageId = parseInt(messagesList.dataset.lastMessageId ?? '-1', 10);
      const response = await fetch(`${API_BASE}/${REPORT_ID}/chat/get`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ report_id: REPORT_ID, last_message_id: lastMessageId })
      });

      if (!response.ok) return;
      const data = await response.json();
      if (!data.success || !Array.isArray(data.messages) || data.messages.length === 0) return;

      removeLoadingMessage();
      const shouldScroll = messagesContainer.scrollTop >= messagesContainer.scrollHeight - messagesContainer.clientHeight - 50;

      for (const msg of data.messages) {
        const item = createMessageElement(msg);
        messagesList.appendChild(item);
        messagesList.dataset.lastMessageId = String(msg.id);
      }

      if (shouldScroll) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }
    } catch (error) {
      console.error('Report chat polling error:', error);
    }
  }

  async function sendMessage(event) {
    event.preventDefault();
    const content = messageInput.value.trim();
    if (!content) return;

    const submitBtn = chatForm.querySelector('button[type="submit"]');
    submitBtn.disabled = true;

    try {
      const response = await fetch(`${API_BASE}/${REPORT_ID}/chat/send`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ report_id: REPORT_ID, content })
      });

      const data = await response.json();
      if (!response.ok || !data.success || !data.message) {
        alert(data.message || 'Failed to send message');
        return;
      }

      removeLoadingMessage();
      messagesList.appendChild(createMessageElement(data.message));
      messagesList.dataset.lastMessageId = String(data.message.id);
      messageInput.value = '';
      charCount.textContent = '0';
      messagesContainer.scrollTop = messagesContainer.scrollHeight;
      messageInput.focus();
    } catch (error) {
      console.error('Send report chat message error:', error);
      alert('Failed to send message');
    } finally {
      submitBtn.disabled = false;
    }
  }

  messageInput.addEventListener('input', (event) => {
    charCount.textContent = String(event.target.value.length);
  });

  chatForm.addEventListener('submit', sendMessage);

  document.addEventListener('DOMContentLoaded', async () => {
    await pollMessages();
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    setInterval(pollMessages, POLL_INTERVAL);
  });
</script>
