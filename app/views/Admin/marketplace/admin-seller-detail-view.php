<link rel="stylesheet" href="/css/app/admin/marketplace/reported.css">

<?php
$sellerId = (int)($sellerId ?? 0);
$seller = $seller ?? null;
?>

<main class="reported-main" role="main" aria-label="Admin Marketplace Seller Detail" data-seller-id="<?= $sellerId ?>">
  <div class="page-header">
    <h1 class="page-title">Seller Profile Moderation</h1>
    <p class="page-subtitle">
      <a href="/dashboard/marketplace/admin/sellers" class="action-btn" style="text-decoration:none;">Back to Seller Moderation</a>
    </p>
    <?php if (!$seller): ?>
      <p class="page-subtitle" style="margin-top:8px;">Seller profile not found in reported marketplace data.</p>
    <?php else: ?>
      <p class="page-subtitle" id="seller-summary-line" style="margin-top:8px;">
        <?= htmlspecialchars(trim(($seller['seller_first_name'] ?? '') . ' ' . ($seller['seller_last_name'] ?? '')) ?: 'Unknown Seller') ?>
        • <?= htmlspecialchars($seller['seller_email'] ?? '') ?>
      </p>
    <?php endif; ?>
  </div>

  <?php if ($seller): ?>
    <div class="reports-table-container" style="padding: var(--space-md); margin-bottom: var(--space-lg);">
      <div class="actions" style="margin-bottom: var(--space-sm);">
        <div class="action-btn" id="summary-total-reports">Reports: 0</div>
        <div class="action-btn" id="summary-open-reports">Open: 0</div>
        <div class="action-btn" id="summary-warning-count">Warnings: 0</div>
        <div class="action-btn" id="summary-ban-status">Status: ACTIVE</div>
      </div>
      <div class="search-filters" style="margin-bottom:0;">
        <div class="search-bar">
          <input type="text" id="moderation-reason" placeholder="Reason for warning / ban / unban">
        </div>
        <div class="filters">
          <select id="report-reference" class="filter-select">
            <option value="">General account action (no report link)</option>
          </select>
          <button class="action-btn review-btn" id="warn-btn" type="button">Warn Seller</button>
          <button class="action-btn" id="ban-toggle-btn" type="button">Ban Seller</button>
        </div>
      </div>
    </div>

    <div class="reports-table-container" style="margin-bottom: var(--space-lg);">
      <table class="reports-table">
        <thead>
          <tr>
            <th>Report</th>
            <th>Order</th>
            <th>Product</th>
            <th>Reporter</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Chat</th>
          </tr>
        </thead>
        <tbody id="seller-related-reports-tbody">
          <tr>
            <td colspan="7" class="empty-description">Loading related reports...</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="reports-table-container">
      <table class="reports-table">
        <thead>
          <tr>
            <th>Action</th>
            <th>Reason</th>
            <th>Report Link</th>
            <th>By Admin</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="seller-history-tbody">
          <tr>
            <td colspan="5" class="empty-description">Loading moderation history...</td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>

<?php if ($seller): ?>
  <script src="/js/app/admin/marketplace/admin-seller-detail.js"></script>
<?php endif; ?>
