<link rel="stylesheet" href="/css/app/user/user-dashboard.css">

<?php
// ── Helpers ──────────────────────────────────────────────────────────────────
function dash_esc(mixed $v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
function dash_initial(string $name): string { return strtoupper(substr(trim($name), 0, 1)) ?: '?'; }
function dash_truncate(string $text, int $len = 180): string {
    $text = strip_tags($text);
    return mb_strlen($text) > $len ? mb_substr($text, 0, $len) . '…' : $text;
}
function dash_time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)       return 'just now';
    if ($diff < 3600)     return floor($diff / 60)   . ' min ago';
    if ($diff < 86400)    return floor($diff / 3600)  . ' hours ago';
    if ($diff < 604800)   return floor($diff / 86400) . ' days ago';
    return date('M j, Y', strtotime($datetime));
}
function dash_format_event_date(string $date): array {
    $ts = strtotime($date);
    return ['day' => date('d', $ts), 'label' => date('M D', $ts)];
}
function dash_format_time_range(?string $start, ?string $end): string {
    $fmt = fn($t) => date('g:i A', strtotime("1970-01-01 $t"));
    if ($start && $end)  return $fmt($start) . '–' . $fmt($end);
    if ($start)          return $fmt($start);
    return 'All Day';
}
function dash_order_status_label(string $status): string {
    return match($status) {
        'yet_to_ship' => 'Yet To Ship',
        'shipped'     => 'Shipped',
        'delivered'   => 'Delivered',
        'cancelled'   => 'Cancelled',
        default       => ucfirst(str_replace('_', ' ', $status)),
    };
}
function dash_order_image(array $order): string {
    if (!empty($order['image_url'])) return $order['image_url'];
    $imgs = json_decode($order['images'] ?? '[]', true);
    if (!empty($imgs[0])) return '/storage/orders/' . $imgs[0];
    return 'https://via.placeholder.com/60x60/1D4ED8/ffffff?text=Item';
}
?>

<!-- Main Dashboard Content -->
<main class="dashboard-main" role="main" aria-label="User Dashboard">

  <!-- Quick Actions Section -->
  <section class="quick-actions">

    <!-- Ask Anything card (uses logged-in user) -->
    <div class="action-card ask-card">
      <h3 class="card-title">Ask Anything</h3>
      <div class="user-info">
        <div class="user-avatar small">
          <span><?= dash_esc(dash_initial($user['first_name'] ?? 'U')) ?></span>
        </div>
        <span class="user-name">
          <?= dash_esc(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
        </span>
      </div>
      <form id="quick-ask-form" onsubmit="submitQuickQuestion(event)">
        <textarea 
          id="quick-ask-input"
          class="quick-ask-textarea" 
          placeholder="Ask others what you need to know..."
          rows="3"
          maxlength="500"
          required
        ></textarea>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:0.5rem;">
          <span id="char-count" style="font-size:0.75rem;color:#6b7280;">0/500</span>
          <button type="submit" class="action-btn primary-btn">Publish</button>
        </div>
      </form>
    </div>

    <!-- Answer Question card (latest open forum question) -->
    <div class="action-card answer-card">
      <h3 class="card-title">Answer Question</h3>
      <?php if ($featuredQuestion): ?>
        <div class="user-info">
          <div class="user-avatar small">
            <span><?= dash_esc(dash_initial($featuredQuestion['first_name'] ?? 'U')) ?></span>
          </div>
          <span class="user-name">
            <?= dash_esc(($featuredQuestion['first_name'] ?? '') . ' ' . ($featuredQuestion['last_name'] ?? '')) ?>
          </span>
        </div>
        <p class="card-description"><?= dash_esc(dash_time_ago($featuredQuestion['created_at'])) ?></p>
        <div class="question-text">
          <?= dash_esc(dash_truncate($featuredQuestion['title'], 160)) ?>
        </div>
        <div class="voting-section">
          <span class="vote-count"><?= dash_esc(str_pad($featuredQuestion['votes'], 2, '0', STR_PAD_LEFT)) ?> Votes</span>
          <span class="answer-count"><?= dash_esc(str_pad($featuredQuestion['answer_count'], 2, '0', STR_PAD_LEFT)) ?> Answers</span>
        </div>
        <button class="action-btn secondary-btn"
                onclick="navigateTo('/dashboard/community/forum/question/<?= (int)$featuredQuestion['id'] ?>')">
          Answer
        </button>
      <?php else: ?>
        <p class="card-description" style="margin-top:1rem;">No open questions yet. Be the first to ask!</p>
        <button class="action-btn secondary-btn" onclick="navigateTo('/dashboard/community/forum')">Browse Forum</button>
      <?php endif; ?>
    </div>

    <!-- Featured Blog card -->
    <div class="action-card blog-card">
      <h3 class="card-title">Featured Blog</h3>
      <?php if ($featuredBlog): ?>
        <div class="user-info">
          <div class="user-avatar small">
            <span><?= dash_esc(dash_initial($featuredBlog['first_name'] ?? 'B')) ?></span>
          </div>
          <span class="user-name">
            <?= dash_esc(($featuredBlog['first_name'] ?? '') . ' ' . ($featuredBlog['last_name'] ?? '')) ?>
          </span>
        </div>
        <div class="blog-image">
          <img src="<?= dash_esc($featuredBlog['image_url'] ?: 'https://via.placeholder.com/300x150/10B981/ffffff?text=Blog') ?>"
               alt="<?= dash_esc($featuredBlog['title']) ?>" loading="lazy">
        </div>
        <h4 class="blog-title"><?= dash_esc($featuredBlog['title']) ?></h4>
        <p class="blog-excerpt"><?= dash_esc(dash_truncate($featuredBlog['body'], 200)) ?></p>
        <button class="see-more-btn"
                onclick="navigateTo('/dashboard/community/blog/<?= (int)$featuredBlog['id'] ?>')">
          See More...
        </button>
      <?php else: ?>
        <p class="card-description" style="margin-top:1rem;">No featured blogs yet.</p>
        <button class="see-more-btn" onclick="navigateTo('/dashboard/community')">Explore Community</button>
      <?php endif; ?>
    </div>
  </section>

  <!-- Two Column Layout -->
  <div class="dashboard-grid">

    <!-- Left Column -->
    <div class="left-column">

      <!-- Featured Posts -->
      <section class="dashboard-section featured-posts">
        <h2 class="section-title">Featured Posts</h2>

        <?php if (!empty($featuredPosts)): ?>
          <?php foreach ($featuredPosts as $post): ?>
            <div class="post-card">
              <div class="post-header">
                <?php if (!empty($post['group_name'])): ?>
                  <div class="chapter-badge"><?= dash_esc($post['group_name']) ?></div>
                <?php endif; ?>
                <div class="post-date"><?= dash_esc(dash_time_ago($post['created_at'])) ?></div>
              </div>
              <h3 class="post-title"><?= dash_esc($post['title']) ?></h3>

              <?php if (!empty($post['image_url'])): ?>
                <div class="event-poster-image">
                  <img src="<?= dash_esc($post['image_url']) ?>"
                       alt="<?= dash_esc($post['title']) ?>" loading="lazy">
                </div>
              <?php endif; ?>

              <p class="post-description"><?= dash_esc(dash_truncate($post['body'], 280)) ?></p>

              <div class="post-actions">
                <button class="see-more-btn"
                        onclick="navigateTo('/dashboard/community/post/<?= (int)$post['id'] ?>')">
                  See More...
                </button>
                <div class="post-stats">
                  <span class="stat-item">👍 <?= (int)$post['likes'] ?></span>
                  <span class="stat-item">💬 <?= (int)$post['comments_count'] ?></span>
                  <span class="stat-item">📤 <?= (int)$post['shares'] ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="post-card" style="text-align:center;padding:2rem;color:var(--text-secondary,#888);">
            <p>No featured posts yet.</p>
            <button class="see-more-btn" onclick="navigateTo('/dashboard/community')">Browse Community</button>
          </div>
        <?php endif; ?>
      </section>

      <!-- Recent Blogs Section -->
      <section class="dashboard-section recent-blogs">
        <h2 class="section-title">Recent Blogs</h2>

        <?php if (!empty($recentBlogs)): ?>
          <div class="blog-grid">
            <?php foreach ($recentBlogs as $blog): ?>
              <div class="blog-card-small"
                   style="cursor:pointer"
                   onclick="navigateTo('/dashboard/community/blog/<?= (int)$blog['id'] ?>')">
                <div class="blog-image-small">
                  <img src="<?= dash_esc($blog['image_url'] ?: 'https://via.placeholder.com/150x100/3B82F6/ffffff?text=Blog') ?>"
                       alt="<?= dash_esc($blog['title']) ?>" loading="lazy">
                </div>
                <div class="blog-content-small">
                  <h4 class="blog-title-small"><?= dash_esc(dash_truncate($blog['title'], 60)) ?></h4>
                  <p class="blog-author">
                    by <?= dash_esc(trim(($blog['first_name'] ?? '') . ' ' . ($blog['last_name'] ?? ''))) ?>
                  </p>
                  <p class="blog-date"><?= date('M j, Y', strtotime($blog['created_at'])) ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p style="color:var(--text-secondary,#888);padding:1rem 0;">No recent blogs available.</p>
        <?php endif; ?>
      </section>

      <!-- Lost & Found Items -->
      <section class="dashboard-section lost-items">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
          <h2 class="section-title" style="margin-bottom:0;">Recent Lost &amp; Found</h2>
          <div style="display:flex;gap:.5rem;">
            <a href="/dashboard/lost-and-found/report-lost-item"
               style="font-size:.75rem;padding:.3rem .7rem;border-radius:6px;background:#fee2e2;color:#b91c1c;text-decoration:none;font-weight:600;">
              + Report Lost
            </a>
            <a href="/dashboard/lost-and-found/report-found-item"
               style="font-size:.75rem;padding:.3rem .7rem;border-radius:6px;background:#d1fae5;color:#065f46;text-decoration:none;font-weight:600;">
              + Report Found
            </a>
          </div>
        </div>

        <?php if (!empty($recentLFItems)): ?>
          <?php foreach ($recentLFItems as $lfItem):
            $isLost   = $lfItem['_type'] === 'lost';
            $imgSrc   = '/assets/placeholders/product.jpeg';
            if (!empty($lfItem['images'][0]['image_path'])) {
                $p = $lfItem['images'][0]['image_path'];
                $imgSrc = (str_starts_with($p, '/') || str_starts_with($p, 'http')) ? $p : '/' . $p;
            }
            $typeLabel  = $isLost ? 'Lost' : 'Found';
            $typeBg     = $isLost ? '#fee2e2' : '#d1fae5';
            $typeColor  = $isLost ? '#b91c1c' : '#065f46';
            $badge      = $lfItem['_badge'];
            $badgeBg    = match(strtolower($badge)) {
                'critical' => '#fef2f2', 'important' => '#fefce8',
                'general'  => '#eff6ff',
                'excellent','good' => '#f0fdf4', 'fair' => '#fefce8', 'poor' => '#fef2f2',
                default    => '#f3f4f6'
            };
            $badgeColor = match(strtolower($badge)) {
                'critical' => '#dc2626', 'important' => '#ca8a04',
                'general'  => '#2563eb',
                'excellent','good' => '#16a34a', 'fair' => '#ca8a04', 'poor' => '#dc2626',
                default    => '#6b7280'
            };
          ?>
            <div class="lost-item-card" style="margin-bottom:.75rem;">
              <div class="item-image">
                <img src="<?= dash_esc($imgSrc) ?>"
                     alt="<?= dash_esc($lfItem['item_name']) ?>"
                     loading="lazy"
                     onerror="this.src='/assets/placeholders/product.jpeg'">
              </div>
              <div class="item-info">
                <div style="display:flex;gap:.4rem;align-items:center;margin-bottom:.35rem;flex-wrap:wrap;">
                  <span style="font-size:.7rem;font-weight:700;padding:.15rem .5rem;border-radius:4px;
                               background:<?= $typeBg ?>;color:<?= $typeColor ?>">
                    <?= $typeLabel ?>
                  </span>
                  <?php if ($badge): ?>
                    <span style="font-size:.7rem;font-weight:600;padding:.15rem .5rem;border-radius:4px;
                                 background:<?= $badgeBg ?>;color:<?= $badgeColor ?>">
                      <?= dash_esc($badge) ?>
                    </span>
                  <?php endif; ?>
                  <span style="font-size:.7rem;color:#6b7280;"><?= dash_esc($lfItem['category'] ?? '') ?></span>
                </div>
                <h4 class="item-title"><?= dash_esc($lfItem['item_name']) ?></h4>
                <p class="item-description"><?= dash_esc(dash_truncate($lfItem['description'] ?? '', 130)) ?></p>
                <div class="item-details">
                  <?php if ($lfItem['_location']): ?>
                    <span class="item-location">📍 <?= dash_esc($lfItem['_location']) ?></span>
                  <?php endif; ?>
                  <span class="item-date">🕒 <?= dash_esc(dash_time_ago($lfItem['_date'])) ?></span>
                </div>
              </div>
              <button class="contact-btn"
                      onclick="navigateTo('/dashboard/lost-and-found/items')">
                View
              </button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color:var(--text-secondary,#888);padding:1rem 0;">No lost or found items reported recently.</p>
        <?php endif; ?>

        <button class="see-more-btn" onclick="navigateTo('/dashboard/lost-and-found/items')">See All Lost &amp; Found Items...</button>
      </section>
    </div>

    <!-- Right Column -->
    <div class="right-column">

      <!-- Upcoming Events -->
      <section class="dashboard-section upcoming-events">
        <h2 class="section-title">Upcoming Events</h2>

        <?php if (!empty($upcomingEvents)): ?>
          <div class="events-list">
            <?php foreach ($upcomingEvents as $event):
              $dateParts = dash_format_event_date($event['event_date']);
              $timeRange = dash_format_time_range($event['event_time_start'], $event['event_time_end']);
            ?>
              <div class="event-item"
                   onclick="navigateTo('/dashboard/community/event/<?= (int)$event['id'] ?>')">
                <div class="event-date">
                  <span class="date-number"><?= dash_esc($dateParts['day']) ?></span>
                  <span class="date-text"><?= dash_esc($dateParts['label']) ?></span>
                </div>
                <div class="event-details">
                  <span class="event-time"><?= dash_esc($timeRange) ?></span>
                  <span class="event-name"><?= dash_esc($event['title']) ?></span>
                  <span class="event-location"><?= dash_esc($event['event_location'] ?? '') ?></span>
                </div>
                <div class="event-status">Available</div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p style="color:var(--text-secondary,#888);padding:1rem 0;">No upcoming events scheduled.</p>
        <?php endif; ?>

        <button class="see-more-btn" onclick="navigateTo('/dashboard/community/events')">View All Events...</button>
      </section>

      <!-- Order Summary -->
      <section class="dashboard-section order-summary">
        <h2 class="section-title">Recent Orders</h2>

        <?php if (!empty($recentOrders)): ?>
          <?php foreach ($recentOrders as $order):
            $statusLabel = dash_order_status_label($order['status']);
            $statusClass = str_replace('_', '-', $order['status']);
            $imgSrc      = dash_order_image($order);
          ?>
            <div class="order-card" onclick="navigateTo('/dashboard/marketplace/orders')">
              <div class="order-status <?= dash_esc($statusClass) ?>"><?= dash_esc($statusLabel) ?></div>
              <div class="order-content">
                <div class="product-image">
                  <img src="<?= dash_esc($imgSrc) ?>"
                       alt="<?= dash_esc($order['title'] ?? 'Product') ?>" loading="lazy">
                </div>
                <div class="order-info">
                  <h4 class="product-name"><?= dash_esc($order['title'] ?? 'Product') ?></h4>
                  <div class="product-price">
                    Rs. <?= number_format((float)($order['unit_price'] ?? 0) * (int)($order['quantity'] ?? 1), 2) ?>
                  </div>
                  <div class="order-details">
                    <span class="order-condition">Status: <?= dash_esc($statusLabel) ?></span>
                    <span class="order-id">Order ID: #<?= (int)$order['id'] ?></span>
                  </div>
                </div>
              </div>
              <div class="order-actions">
                <?php if (in_array($order['status'], ['yet_to_ship', 'shipped'])): ?>
                  <button class="track-btn">Track Order</button>
                <?php elseif ($order['status'] === 'delivered'): ?>
                  <button class="review-btn">Leave Review</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color:var(--text-secondary,#888);padding:1rem 0;">You haven't placed any orders yet.</p>
        <?php endif; ?>

        <button class="see-more-btn" onclick="navigateTo('/dashboard/marketplace/orders')">View All Orders...</button>
      </section>

      <!-- Quick Stats -->
      <section class="dashboard-section quick-stats">
        <h2 class="section-title">Your Activity</h2>

        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">📝</div>
            <div class="stat-value"><?= (int)($stats['questions_asked'] ?? 0) ?></div>
            <div class="stat-label">Questions Asked</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">💡</div>
            <div class="stat-value"><?= (int)($stats['answers_given'] ?? 0) ?></div>
            <div class="stat-label">Answers Given</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">🛒</div>
            <div class="stat-value"><?= (int)($stats['orders_placed'] ?? 0) ?></div>
            <div class="stat-label">Orders Placed</div>
          </div>

          <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-value"><?= (int)($stats['blogs_written'] ?? 0) ?></div>
            <div class="stat-label">Blogs Written</div>
          </div>
        </div>
      </section>
    </div>
  </div>
</main>

<!-- Quick Ask Styles -->
<style>
.quick-ask-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px;
  font-family: inherit;
  font-size: 0.875rem;
  line-height: 1.5;
  resize: vertical;
  transition: border-color 0.2s, box-shadow 0.2s;
  background: #fafafa;
}

.quick-ask-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  background: white;
}

.quick-ask-textarea::placeholder {
  color: #9ca3af;
}

#quick-ask-form {
  margin-top: 0.75rem;
}
</style>

<!-- Minimal JavaScript -->
<script src="/js/app/user-dashboard.js"></script>
<script>
// Character counter
document.getElementById('quick-ask-input')?.addEventListener('input', function() {
  const count = this.value.length;
  const counter = document.getElementById('char-count');
  if (counter) {
    counter.textContent = count + '/500';
    counter.style.color = count > 450 ? '#dc2626' : '#6b7280';
  }
});

// Submit quick question
async function submitQuickQuestion(event) {
  event.preventDefault();
  
  const textarea = document.getElementById('quick-ask-input');
  const question = textarea.value.trim();
  
  if (!question) {
    alert('Please enter a question');
    return;
  }
  
  if (question.length < 10) {
    alert('Question must be at least 10 characters long');
    return;
  }
  
  const submitBtn = event.target.querySelector('button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Publishing...';
  
  try {
    const response = await fetch('/dashboard/community/forum/quick-ask', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ question: question })
    });
    
    const data = await response.json();
    
    if (data.success) {
      textarea.value = '';
      document.getElementById('char-count').textContent = '0/500';
      
      // Show success message
      const successMsg = document.createElement('div');
      successMsg.style.cssText = 'position:fixed;top:20px;right:20px;background:#10b981;color:white;padding:1rem 1.5rem;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);z-index:9999;font-weight:500;';
      successMsg.textContent = '✓ Question posted successfully!';
      document.body.appendChild(successMsg);
      
      setTimeout(() => {
        successMsg.style.transition = 'opacity 0.3s';
        successMsg.style.opacity = '0';
        setTimeout(() => successMsg.remove(), 300);
      }, 3000);
      
      // Optionally redirect to forum
      if (data.questionId) {
        setTimeout(() => {
          window.location.href = '/dashboard/community/forum';
        }, 1500);
      }
    } else {
      alert(data.message || 'Failed to post question. Please try again.');
    }
  } catch (error) {
    console.error('Error posting question:', error);
    alert('An error occurred. Please try again.');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Publish';
  }
}
</script>