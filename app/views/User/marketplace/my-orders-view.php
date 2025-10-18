<link rel="stylesheet" href="/css/app/user/marketplace/my-orders.css">

<!-- Main Content Area -->
<main class="orders-main" role="main" aria-label="My Orders">
  
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/marketplace" class="breadcrumb__link">Marketplace</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        My Orders
      </li>
    </ol>
  </nav>

  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">My Orders</h1>
    <div class="orders-count">5 orders found</div>
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
        <span class="tab-count">5</span>
      </button>
      <button class="tab-button" 
              data-tab="pending" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-pending"
              tabindex="-1">
        Pending
        <span class="tab-count">1</span>
      </button>
      <button class="tab-button" 
              data-tab="shipped" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-shipped"
              tabindex="-1">
        Yet to Shipped
        <span class="tab-count">1</span>
      </button>
      <button class="tab-button" 
              data-tab="delivered" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-delivered"
              tabindex="-1">
        Delivered
        <span class="tab-count">2</span>
      </button>
      <button class="tab-button" 
              data-tab="cancelled" 
              role="tab" 
              aria-selected="false" 
              aria-controls="tab-content-cancelled"
              tabindex="-1">
        Cancelled
        <span class="tab-count">1</span>
      </button>
    </div>
  </nav>

  <!-- Tab Content -->
  <div class="orders-container">
    
    <!-- All Orders Tab -->
    <div class="tab-content" data-tab-content="all" id="tab-content-all" role="tabpanel" aria-labelledby="all-tab">
      <div class="orders-list">
        
        <!-- Order 1 -->
        <article class="order-item" data-order-id="374836393">
          <div class="order-image">
            <img src="https://via.placeholder.com/120x120/1e3a8a/ffffff?text=UCSC+Tshirt" alt="UCSC Tshirt">
          </div>
          
          <div class="order-details">
            <div class="order-header">
              <h3 class="order-title">UCSC Tshirt</h3>
              <div class="order-price">Rs. 4,000</div>
            </div>
            
            <div class="order-meta">
              <div class="order-id">Order ID: #374836393</div>
              <div class="order-date">Ordered on: March 15, 2024</div>
              <div class="order-quantity">Quantity: 2</div>
            </div>
            
            <div class="order-status">
              <span class="status-badge status-badge--pending">Yet to Ship</span>
              <div class="status-message">Your order is being prepared for shipment</div>
            </div>
          </div>
          
          <div class="order-actions">
            <button class="btn btn--secondary btn--small" data-action="contact-seller" data-order-id="374836393">
              Contact Seller
            </button>
            <button class="btn btn--primary btn--small" data-action="track-order" data-order-id="374836393">
              Track Order
            </button>
          </div>
        </article>

        <!-- Order 2 -->
        <article class="order-item" data-order-id="58495749">
          <div class="order-image">
            <img src="https://via.placeholder.com/120x120/374151/ffffff?text=Wrist+Band" alt="UCSC Wrist Band">
          </div>
          
          <div class="order-details">
            <div class="order-header">
              <h3 class="order-title">UCSC Wrist Band</h3>
              <div class="order-price">Rs. 600</div>
            </div>
            
            <div class="order-meta">
              <div class="order-id">Order ID: #58495749</div>
              <div class="order-date">Ordered on: March 10, 2024</div>
              <div class="order-quantity">Quantity: 1</div>
            </div>
            
            <div class="order-status">
              <span class="status-badge status-badge--delivered">Delivered</span>
              <div class="status-message">Delivered on March 12, 2024</div>
            </div>
          </div>
          
          <div class="order-actions">
            <button class="btn btn--secondary btn--small" data-action="contact-seller" data-order-id="58495749">
              Contact Seller
            </button>
            <button class="btn btn--outline btn--small" data-action="reorder" data-order-id="58495749">
              Reorder
            </button>
          </div>
        </article>

        <!-- Order 3 -->
        <article class="order-item" data-order-id="58495750">
          <div class="order-image">
            <img src="https://via.placeholder.com/120x120/16a34a/ffffff?text=Cricket+Jersey" alt="UOC Cricket Jersey">
          </div>
          
          <div class="order-details">
            <div class="order-header">
              <h3 class="order-title">UOC Cricket Jersey</h3>
              <div class="order-price">Rs. 1,800</div>
            </div>
            
            <div class="order-meta">
              <div class="order-id">Order ID: #58495750</div>
              <div class="order-date">Ordered on: March 8, 2024</div>
              <div class="order-quantity">Quantity: 1</div>
            </div>
            
            <div class="order-status">
              <span class="status-badge status-badge--shipped">Shipped</span>
              <div class="status-message">Expected delivery: March 20, 2024</div>
            </div>
          </div>
          
          <div class="order-actions">
            <button class="btn btn--secondary btn--small" data-action="contact-seller" data-order-id="58495750">
              Contact Seller
            </button>
            <button class="btn btn--primary btn--small" data-action="track-order" data-order-id="58495750">
              Track Order
            </button>
          </div>
        </article>

        <!-- Order 4 -->
        <article class="order-item" data-order-id="58495751">
          <div class="order-image">
            <img src="https://via.placeholder.com/120x120/dc2626/ffffff?text=Notebook" alt="UCSC Notebook">
          </div>
          
          <div class="order-details">
            <div class="order-header">
              <h3 class="order-title">UCSC Notebook</h3>
              <div class="order-price">Rs. 250</div>
            </div>
            
            <div class="order-meta">
              <div class="order-id">Order ID: #58495751</div>
              <div class="order-date">Ordered on: March 5, 2024</div>
              <div class="order-quantity">Quantity: 3</div>
            </div>
            
            <div class="order-status">
              <span class="status-badge status-badge--delivered">Delivered</span>
              <div class="status-message">Delivered on March 7, 2024</div>
            </div>
          </div>
          
          <div class="order-actions">
            <button class="btn btn--secondary btn--small" data-action="contact-seller" data-order-id="58495751">
              Contact Seller
            </button>
            <button class="btn btn--outline btn--small" data-action="reorder" data-order-id="58495751">
              Reorder
            </button>
          </div>
        </article>

        <!-- Order 5 -->
        <article class="order-item" data-order-id="58495752">
          <div class="order-image">
            <img src="https://via.placeholder.com/120x120/6b7280/ffffff?text=Cancelled" alt="UCSC Cap">
          </div>
          
          <div class="order-details">
            <div class="order-header">
              <h3 class="order-title">UCSC Cap</h3>
              <div class="order-price">Rs. 800</div>
            </div>
            
            <div class="order-meta">
              <div class="order-id">Order ID: #58495752</div>
              <div class="order-date">Ordered on: March 1, 2024</div>
              <div class="order-quantity">Quantity: 1</div>
            </div>
            
            <div class="order-status">
              <span class="status-badge status-badge--cancelled">Cancelled</span>
              <div class="status-message">Order cancelled by seller - Out of stock</div>
            </div>
          </div>
          
          <div class="order-actions">
            <button class="btn btn--secondary btn--small" data-action="contact-seller" data-order-id="58495752">
              Contact Seller
            </button>
            <button class="btn btn--outline btn--small" data-action="reorder" data-order-id="58495752">
              Reorder
            </button>
          </div>
        </article>

      </div>
    </div>

    <!-- Other tab contents will be filtered by JavaScript -->
    <div class="tab-content is-hidden" data-tab-content="pending" id="tab-content-pending" role="tabpanel" aria-labelledby="pending-tab">
      <!-- Content will be populated by JavaScript -->
    </div>

    <div class="tab-content is-hidden" data-tab-content="shipped" id="tab-content-shipped" role="tabpanel" aria-labelledby="shipped-tab">
      <!-- Content will be populated by JavaScript -->
    </div>

    <div class="tab-content is-hidden" data-tab-content="delivered" id="tab-content-delivered" role="tabpanel" aria-labelledby="delivered-tab">
      <!-- Content will be populated by JavaScript -->
    </div>

    <div class="tab-content is-hidden" data-tab-content="cancelled" id="tab-content-cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
      <!-- Content will be populated by JavaScript -->
    </div>

  </div>

  <!-- Contact Seller Modal -->
  <div class="modal-overlay" id="contact-modal" role="dialog" aria-labelledby="contact-title" aria-modal="true" style="display: none;">
    <div class="modal modal--large">
      <div class="modal-header">
        <h2 id="contact-title" class="modal-title">Contact Seller</h2>
        <button class="modal-close" aria-label="Close contact modal">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>
      
      <div class="modal-body">
        <!-- Seller Information -->
        <div class="seller-contact-info">
          <div class="seller-avatar">
            <img src="https://via.placeholder.com/64x64/0466C8/ffffff?text=SU" alt="Seller avatar">
          </div>
          <div class="seller-details">
            <h3 class="seller-name">Students Union of UCSC</h3>
            <div class="seller-rating">
              <div class="rating-stars">
                ★★★★★
              </div>
              <span class="rating-text">4.8 (142 reviews)</span>
            </div>
          </div>
        </div>

        <!-- Contact Options -->
        <div class="contact-options">
          <div class="contact-option">
            <div class="contact-icon">
              <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="contact-details">
              <div class="contact-label">Phone Number</div>
              <div class="contact-value">+94 11 250 3200</div>
            </div>
            <button class="btn btn--secondary btn--small" data-action="call">Call</button>
          </div>

          <div class="contact-option">
            <div class="contact-icon">
              <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path d="M3 8l7.89 3.26a2 2 0 001.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="contact-details">
              <div class="contact-label">Email</div>
              <div class="contact-value">su@ucsc.cmb.ac.lk</div>
            </div>
            <button class="btn btn--secondary btn--small" data-action="email">Email</button>
          </div>
        </div>

        <!-- Chat Section -->
        <div class="chat-section">
          <h4 class="chat-title">Send a Message</h4>
          
          <div class="chat-window" id="chat-window">
            <div class="chat-messages">
              <div class="chat-message chat-message--received">
                <div class="message-avatar">
                  <img src="https://via.placeholder.com/32x32/0466C8/ffffff?text=SU" alt="Seller">
                </div>
                <div class="message-content">
                  <div class="message-text">Hello! How can I help you with your order?</div>
                  <div class="message-time">2 hours ago</div>
                </div>
              </div>
            </div>
          </div>

          <form class="chat-form" id="chat-form">
            <div class="chat-input-container">
              <textarea 
                class="chat-input" 
                id="chat-input" 
                placeholder="Type your message here..."
                rows="2"
                required></textarea>
              <button type="submit" class="chat-send-btn" aria-label="Send message">
                <svg width="20" height="20" fill="none" viewBox="0 0 20 20">
                  <path d="M2 10l8-8v5h8v6h-8v5l-8-8z" fill="currentColor"/>
                </svg>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</main>

<!-- JavaScript -->
<script type="module" src="/js/app/marketplace/my-orders.js"></script>
</body>
</html>