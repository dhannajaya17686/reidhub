<link rel="stylesheet" href="/css/app/user/marketplace/my-cart.css">

<!-- Main Content Area -->
<main class="cart-main" role="main" aria-label="Shopping Cart">
  <!-- Breadcrumb Navigation -->
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item">
        <a href="/dashboard/marketplace/merch-store" class="breadcrumb__link">Marketplace</a>
      </li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">My Cart</li>
    </ol>
  </nav>

  <!-- Page Header -->
  <div class="page-header">
    <h1 class="page-title">My Cart</h1>
    <div class="cart-count" id="cart-count">0 items in your cart</div>
  </div>

  <div class="cart-container">
    <!-- Cart Items -->
    <section class="cart-items" aria-label="Cart Items" id="cart-items"></section>

    <!-- Order Summary -->
    <aside class="order-summary" aria-label="Order Summary">
      <div class="summary-card">
        <h2 class="summary-title">Order Summary</h2>

        <!-- Detailed bill will be rendered here by JS -->
        <div class="summary-details" id="summary-details">
          <div class="summary-line summary-line--total">
            <span class="summary-label">Total</span>
            <span class="summary-value summary-value--total" id="total">Rs. 0</span>
          </div>
        </div>

        <div class="checkout-section">
          <a class="btn btn--primary btn--large btn--full-width" id="checkout-btn" href="/dashboard/marketplace/checkout">
            Proceed to Checkout
          </a>
        </div>
      </div>
    </aside>
  </div>
</main>

<!-- JavaScript -->
<script type="module" src="/js/app/marketplace/my-cart.js"></script>
</body>
</html>