<link rel="stylesheet" href="/css/app/user/marketplace/my-orders.css">

<!-- Main Content Area -->
<main class="orders-main" role="main" aria-label="My Orders">
  
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/dashboard/marketplace/merch-store" class="breadcrumb__link">Marketplace</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        My Orders
      </li>
    </ol>
  </nav>

  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">My Orders</h1>
    <div class="orders-count">0 orders found</div>
  </div>

  <!-- Tab Navigation -->
  <nav class="tab-navigation" aria-label="Order status filter">
    <div class="tab-list" role="tablist">
      <button class="tab-button tab-button--active" 
              data-tab="all" 
              role="tab" 
              aria-selected="true" 
              aria-controls="tab-content-all"
              tabindex="0">
        All Orders
        <span class="tab-count">0</span>
      </button>
      <button class="tab-button" 
              data-tab="pending" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-pending"
              tabindex="-1">
        Pending
        <span class="tab-count">0</span>
      </button>
      <button class="tab-button" 
              data-tab="shipped" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-shipped"
              tabindex="-1">
        Yet to Shipped
        <span class="tab-count">0</span>
      </button>
      <button class="tab-button" 
              data-tab="delivered" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-delivered"
              tabindex="-1">
        Delivered
        <span class="tab-count">0</span>
      </button>
      <button class="tab-button" 
              data-tab="cancelled" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-cancelled"
              tabindex="-1">
        Cancelled
        <span class="tab-count">0</span>
      </button>
    </div>
  </nav>

  <!-- Tab Content -->
  <div class="orders-container">
    
    <!-- All Orders Tab -->
    <div class="tab-content" data-tab-content="all" id="tab-content-all" role="tabpanel" aria-labelledby="all-tab">
      <div class="orders-list"></div>
    </div>

    <!-- Other tab contents will be filtered by JavaScript -->
    <div class="tab-content is-hidden" data-tab-content="pending" id="tab-content-pending" role="tabpanel" aria-labelledby="pending-tab">
      <div class="orders-list"></div>
    </div>

    <div class="tab-content is-hidden" data-tab-content="shipped" id="tab-content-shipped" role="tabpanel" aria-labelledby="shipped-tab">
      <div class="orders-list"></div>
    </div>

    <div class="tab-content is-hidden" data-tab-content="delivered" id="tab-content-delivered" role="tabpanel" aria-labelledby="delivered-tab">
      <div class="orders-list"></div>
    </div>

    <div class="tab-content is-hidden" data-tab-content="cancelled" id="tab-content-cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
      <div class="orders-list"></div>
    </div>

  </div>
</main>

<!-- Chat Popup Modal -->
<div class="chat-modal" id="chat-modal" aria-hidden="true" role="dialog" aria-labelledby="chat-modal-title">
  <div class="chat-modal-backdrop" aria-label="Close chat"></div>
  <div class="chat-modal-container">
    
    <!-- Chat Header -->
    <div class="chat-header">
      <div class="chat-info">
        <div class="seller-avatar">
          <img src="/images/placeholders/user.png" alt="Seller" id="seller-avatar">
        </div>
        <div class="chat-details">
          <h2 class="chat-modal-title" id="chat-modal-title">Chat with Seller</h2>
          <div class="order-info">
            <span class="order-title" id="chat-order-title">Order Title</span>
            <span class="order-id" id="chat-order-id">#12345</span>
          </div>
        </div>
      </div>
      <button class="chat-close" aria-label="Close chat">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>
    </div>

    <!-- Chat Messages -->
    <div class="chat-messages" id="chat-messages">
      <div class="chat-loading">
        <div class="loading-spinner"></div>
        <span>Loading messages...</span>
      </div>
    </div>

    <!-- Chat Input -->
    <div class="chat-input-container">
      <form class="chat-form" id="chat-form">
        <div class="chat-input-wrapper">
          <textarea 
            class="chat-input" 
            id="chat-input" 
            placeholder="Type your message..."
            rows="1"
            maxlength="500"
          ></textarea>
          <button type="submit" class="chat-send" aria-label="Send message">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        <div class="chat-char-count" id="chat-char-count">0/500</div>
      </form>
    </div>

  </div>
</div>

<!-- JavaScript -->
<script type="module" src="/js/app/marketplace/my-orders.js"></script>
<script src="/js/app/marketplace/my-orders-chat.js"></script>
</body>
</html>