<?php
$tx = $transaction ?? null;
if (!$tx) { echo '<p>Transaction not found.</p>'; return; }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<link rel="stylesheet" href="/css/app/user/marketplace/my-cart.css">

<main class="cart-main" role="main" aria-label="Transaction">
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <ol class="breadcrumb__list">
      <li class="breadcrumb__item"><a class="breadcrumb__link" href="/dashboard/marketplace/merch-store">Marketplace</a></li>
      <li class="breadcrumb__item"><a class="breadcrumb__link" href="/dashboard/marketplace/transactions">Transactions</a></li>
      <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">TX<?php echo (int)$tx['id']; ?></li>
    </ol>
  </nav>

  <header class="page-header">
    <h1 class="page-title">Transaction TX<?php echo (int)$tx['id']; ?></h1>
    <div class="cart-count">
      Placed on <?php echo h(date('Y-m-d H:i', strtotime($tx['created_at']))); ?> • 
      <?php echo (int)$tx['item_count']; ?> item<?php echo $tx['item_count'] === 1 ? '' : 's'; ?> •
      Total Rs. <?php echo number_format($tx['total_amount'], 2, '.', ','); ?>
    </div>
  </header>

  <div class="cart-container">
    <section class="cart-items" aria-label="Orders">
      <?php foreach (($tx['orders'] ?? []) as $o): ?>
        <article class="cart-item">
          <div class="item-image">
            <img src="<?php echo h($o['image']); ?>" alt="<?php echo h($o['product_title']); ?>" onerror="this.src='/images/placeholders/product.png'">
          </div>
          <div class="item-details">
            <div class="item-header">
              <h3 class="item-title"><?php echo h($o['product_title']); ?></h3>
              <div class="item-price">Rs. <?php echo number_format($o['unit_price'], 2, '.', ','); ?></div>
            </div>

            <div class="item-meta">
              <div>Qty: <strong><?php echo (int)$o['quantity']; ?></strong></div>
              <div>Payment: <strong><?php echo $o['payment_method'] === 'cash_on_delivery' ? 'Cash on Delivery' : 'Pre-order'; ?></strong></div>
              <div>Status: <strong><?php echo h($o['status']); ?></strong></div>
              <?php if (!empty($o['slip_path'])): ?>
                <div>Slip: <a href="<?php echo h($o['slip_path']); ?>" target="_blank" rel="noopener">View</a></div>
              <?php endif; ?>
              <div>Seller: <strong><?php echo h($o['seller_name']); ?></strong></div>
              <div>Ordered at: <time datetime="<?php echo h($o['created_at']); ?>"><?php echo h(date('Y-m-d H:i', strtotime($o['created_at']))); ?></time></div>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

    <aside class="order-summary">
      <div class="summary-card">
        <h2 class="summary-title">Summary</h2>
        <?php foreach (($tx['orders'] ?? []) as $o): ?>
          <div class="summary-line">
            <span class="summary-label"><?php echo h($o['product_title']); ?> × <?php echo (int)$o['quantity']; ?></span>
            <span class="summary-value">Rs. <?php echo number_format($o['quantity'] * $o['unit_price'], 2, '.', ','); ?></span>
          </div>
        <?php endforeach; ?>
        <hr class="summary-divider">
        <div class="summary-line summary-line--total">
          <span class="summary-label">Total</span>
          <span class="summary-value summary-value--total">Rs. <?php echo number_format($tx['total_amount'], 2, '.', ','); ?></span>
        </div>

        <div class="checkout-section" style="margin-top:12px;">
          <a class="btn btn--secondary btn--full-width" href="/dashboard/marketplace/transactions/invoice?id=<?php echo (int)$tx['id']; ?>">
            Download Invoice
          </a>
        </div>
      </div>
    </aside>
  </div>
</main>