<link rel="stylesheet" href="/css/app/globals.css">
<link rel="stylesheet" href="/css/app/user/community/community.css">
<link rel="stylesheet" href="/css/app/user/community/blog-view.css">

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="breadcrumb__list">
    <li class="breadcrumb__item">
      <a href="/dashboard/community/admin?tab=clubs-societies" class="breadcrumb__link">Clubs & Societies</a>
    </li>
    <li class="breadcrumb__item breadcrumb__item--current" aria-current="page">
      <?= htmlspecialchars($data['club']['name'] ?? 'Club') ?>
    </li>
  </ol>
</nav>

<main class="blog-view-main" role="main" aria-label="Club Details">
  <div class="blog-container">
    <header class="blog-header">
      <h1 class="blog-title"><?= htmlspecialchars($data['club']['name'] ?? 'Untitled Club') ?></h1>

      <div class="blog-image">
        <img src="<?= htmlspecialchars($data['club']['image_url'] ?? 'https://via.placeholder.com/900x400/667EEA/ffffff?text=' . urlencode(substr($data['club']['name'] ?? 'C', 0, 1))) ?>" alt="<?= htmlspecialchars($data['club']['name'] ?? 'Club image') ?>">
      </div>

      <div class="blog-meta">
        <span class="blog-author">Category: <?= htmlspecialchars(ucfirst($data['club']['category'] ?? 'other')) ?></span>
        <span class="blog-separator">|</span>
        <span class="blog-published"><?= (int)($data['club']['actual_member_count'] ?? $data['club']['member_count'] ?? 0) ?> members</span>
        <span class="blog-separator">|</span>
        <span class="blog-views">Created <?= !empty($data['club']['created_at']) ? date('M j, Y', strtotime($data['club']['created_at'])) : '-' ?></span>
      </div>

      <div class="blog-creator">
        <p><strong>Created by:</strong> <?= htmlspecialchars(($data['club']['creator_first_name'] ?? '') . ' ' . ($data['club']['creator_last_name'] ?? '')) ?></p>
      </div>
    </header>

    <article class="blog-content">
      <?= nl2br(htmlspecialchars($data['club']['description'] ?? 'No description available for this club yet.')) ?>
    </article>

    <div class="blog-actions">
      <a href="/dashboard/community/admin?tab=clubs-societies" class="btn btn--secondary">Back to Clubs & Societies</a>
    </div>
  </div>
</main>
