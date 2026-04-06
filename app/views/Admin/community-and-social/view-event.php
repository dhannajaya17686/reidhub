<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/user/community/community.css">
<link rel="stylesheet" href="/css/app/user/community/blog-view.css">

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="breadcrumb__list">
    <li class="breadcrumb__item">
      <a href="/dashboard/community/admin?tab=events" class="breadcrumb__link">Events</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      <?= htmlspecialchars($data['event']['title'] ?? 'Event') ?>
    </li>
  </ol>
</nav>

<main class="blog-view-main" role="main" aria-label="Event Details">
  <div class="blog-container">
    <header class="blog-header">
      <h1 class="blog-title"><?= htmlspecialchars($data['event']['title'] ?? 'Untitled Event') ?></h1>

      <div class="blog-image">
        <img src="<?= htmlspecialchars($data['event']['image_url'] ?? 'https://via.placeholder.com/900x400/E74C3C/ffffff?text=' . urlencode(substr($data['event']['title'] ?? 'E', 0, 1))) ?>" alt="<?= htmlspecialchars($data['event']['title'] ?? 'Event image') ?>">
      </div>

      <div class="blog-meta">
        <span class="blog-author"><?= htmlspecialchars($data['event']['category'] ?? '-') ?></span>
        <span class="blog-separator">|</span>
        <span class="blog-published"><?= !empty($data['event']['event_date']) ? date('M j, Y g:ia', strtotime($data['event']['event_date'])) : '-' ?></span>
        <span class="blog-separator">|</span>
        <span class="blog-views"><?= (int)($data['event']['attendee_count'] ?? 0) ?> attending</span>
      </div>

      <div class="blog-creator">
        <p><strong>Location:</strong> <?= htmlspecialchars($data['event']['location'] ?? '-') ?></p>
        <p><strong>Created by:</strong> <?= htmlspecialchars(($data['event']['creator_first_name'] ?? '') . ' ' . ($data['event']['creator_last_name'] ?? '')) ?></p>
      </div>
    </header>

    <article class="blog-content">
      <?= nl2br(htmlspecialchars($data['event']['description'] ?? 'No description available for this event yet.')) ?>
    </article>

    <?php if (!empty($data['attendees'])): ?>
    <div class="attendees-section" style="margin-top: 2rem;">
      <h3>Attendees (<?= count($data['attendees']) ?>)</h3>
      <div class="attendees-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem; margin-top: 1rem;">
        <?php foreach ($data['attendees'] as $attendee): ?>
        <div class="attendee-card" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.75rem; background: #f9fafb;">
          <p style="margin: 0; font-weight: 600;"><?= htmlspecialchars(($attendee['first_name'] ?? '') . ' ' . ($attendee['last_name'] ?? '')) ?></p>
          <p style="margin: 0.35rem 0 0; color: #6b7280; font-size: 0.875rem;"><?= htmlspecialchars($attendee['email'] ?? '-') ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="blog-actions">
      <a href="/dashboard/community/admin?tab=events" class="btn btn--secondary">Back to Events</a>
    </div>
  </div>
</main>
